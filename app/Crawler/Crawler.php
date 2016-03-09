<?php

namespace App\Crawler;

use App\Url;
use App\Search;
use App\Resource;
use Hedii\Extractors\Extractor;

class Crawler
{
    /**
     * The search Eloquent model.
     *
     * @var \App\Search
     */
    protected $search;

    /**
     * The Eloquent model for the search entry point url.
     *
     * @var \App\Url
     */
    protected $entryPoint;

    /**
     * The search's domain name.
     *
     * @var string
     */
    protected $domainName;

    /**
     * Whether or not the search has to be limited to a domain name.
     *
     * @var bool
     */
    protected $domainLimit;

    /**
     * @var \Hedii\Extractors\Extractor
     */
    protected $extractor;

    /**
     * Crawler constructor.
     *
     * @param \Hedii\Extractors\Extractor $extractor
     */
    public function __construct(Extractor $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * Run the crawler until the search is finished or until
     * the search is deleted.
     *
     * @param \App\Search $search
     * @return bool
     */
    public function run(Search $search)
    {
        $this->search = $search;

        if (!$this->searchHasBeenDeleted()) {
            $this->domainName = $this->getDomainName($this->search->entrypoint);
            $this->domainLimit = (bool) $this->search->domain_limit;

            return $this->crawl();
        }

        return false;
    }

    /**
     * All the logic for this class.
     *
     * @return bool
     */
    protected function crawl()
    {
        $resources = $this->extractor->searchFor(['urls', 'emails'])
            ->at($this->search->entrypoint)
            ->get();

        $this->storeUrls($resources['urls']);
        $this->storeEmails($resources['emails']);

        // crawl all search's url
        while ($this->searchHasNotCrawledUrl()) {
            $url = $this->getNextNotCrawledUrl();
            $resources = $this->extractor->searchFor(['urls', 'emails'])
                ->at($url->name)
                ->get();

            $this->storeUrls($resources['urls']);
            $this->storeEmails($resources['emails']);
            $this->markAsCrawled($url);

            // check if search has been deleted
            if ($this->searchHasBeenDeleted()) {
                return false;
            }
        }

        // this search is finished!
        $this->markAsFinished($this->search);

        return false;
    }

    /**
     * Store urls in the database.
     *
     * @param $urls
     * @return $this
     */
    protected function storeUrls($urls)
    {
        if (count($urls) > 0) {
            foreach ($urls as $url) {
                $url = $this->cleanUrl($url);

                if (
                    // if url is not a valid url, continue
                    (!$this->isValidUrl($url)) ||
                    // or, if domainLimit, get only the same domain urls
                    ($this->domainLimit && ($this->getDomainName($url) !== $this->domainName)) ||
                    // we don't want media files like images
                    $this->isMediaFile($url)
                ) {
                    continue;
                }

                $theUrl = new Url();
                $theUrl->name = $url;
                $theUrl->crawled = false;
                $theUrl->user_id = $this->search->user_id;

                if (!$this->searchHasUrl($url)) {
                    $this->search->urls()->save($theUrl);
                }
            }
        }

        return $this;
    }

    /**
     * Store emails in the database.
     *
     * @param $emails
     * @return $this
     */
    protected function storeEmails($emails)
    {
        if (count($emails) > 0) {
            foreach ($emails as $email) {
                if (!$this->isValidEmail($email)) {
                    continue;
                }

                $resource = new Resource();
                $resource->type = $this->search->type;
                $resource->name = $email;
                $resource->user_id = $this->search->user_id;
                $resource->search_id = $this->search->id;

                if (!$this->searchHasEmail($email)) {
                    $this->search->resources()->save($resource);
                }
            }
        }

        return $this;
    }

    /**
     * Check if the search already has this url.
     *
     * @param string $url
     * @return bool
     */
    protected function searchHasUrl($url)
    {
        return $this->search->urls()
            ->where(['name' => $url])
            ->first();
    }

    /**
     * Check if the search already has this email.
     *
     * @param string $email
     * @return bool
     */
    protected function searchHasEmail($email)
    {
        return $this->search->resources()->where([
            'type' => $this->search->type,
            'name' => $email
        ])->first();
    }

    /**
     * Check if the search has been deleted in the database.
     *
     * @return bool
     */
    protected function searchHasBeenDeleted()
    {
        return Search::find($this->search->id)->count() == 0;
    }

    /**
     * Check if the search still has urls that have not been
     * crawled yet.
     *
     * @return bool
     */
    protected function searchHasNotCrawledUrl()
    {
        return $this->search->urls()->where(['crawled' => false])->first();
    }

    /**
     * Get the search's url that has not been crawled yet.
     *
     * @return mixed
     */
    protected function getNextNotCrawledUrl()
    {
        return $this->search->urls()->where(['crawled' => false])->first();
    }

    /**
     * Update the url in the database: mark it as crawled for url.
     *
     * @param \App\Url $url
     * @return $this
     */
    protected function markAsCrawled(Url $url)
    {
        $url->crawled = true;
        $url->save();

        return $this;
    }

    /**
     * Update the search in the database: mark it as finished.
     *
     * @param \App\Search $search
     * @return $this
     */
    protected function markAsFinished(Search $search)
    {
        $search->finished = true;
        $search->save();

        return $this;
    }

    /**
     * A wrapper for php parse_url host function.
     *
     * @param string $url
     * @return mixed
     */
    protected function getDomainName($url)
    {
        return parse_url($url, PHP_URL_HOST);
    }

    /**
     * A wrapper for php filter_var url function.
     *
     * @param string $url
     * @return bool
     */
    protected function isValidUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * A wrapper for php filter_var email function.
     *
     * @param string $email
     * @return bool
     */
    protected function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Remove unwanted character at the end of the url string.
     *
     * @param string $url
     * @return string
     */
    protected function cleanUrl($url)
    {
        return rtrim(rtrim($url, '#'), '/');
    }

    /**
     * Check if a given url is a media file url.
     *
     * @param string $url
     * @return bool
     */
    protected function isMediaFile($url)
    {
        return ends_with($url, [
            '.jpg',
            '.jp2',
            '.jpeg',
            '.raw',
            '.png',
            '.gif',
            '.tiff',
            '.bmp',
            '.svg',
            '.fla',
            '.swf',
            '.css',
            '.js'
        ]);
    }
}
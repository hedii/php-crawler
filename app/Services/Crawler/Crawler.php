<?php

namespace App\Services\Crawler;

use App\Email;
use App\Search;
use App\Url;
use Hedii\Extractors\Extractor;

class Crawler
{
    /**
     * The search instance.
     *
     * @var \App\Search
     */
    private $search;

    /**
     * The extractor instance.
     *
     * @var \Hedii\Extractors\Extractor
     */
    private $extractor;

    /**
     * Crawler constructor.
     *
     * @param \App\Search $search
     */
    public function __construct(Search $search)
    {
        $this->search = $search;
        $this->extractor = new Extractor();
    }

    /**
     * Run the crawler until the search is finished or until
     * the search is deleted.
     *
     * @return bool
     */
    public function run(): bool
    {
        $this->search->update(['status' => Search::STATUS_RUNNING]);

        // we first need to crawl the search's entry point url
        $this->crawl($this->search->url, $entryPoint = true);

        // next, we crawl all search's urls
        while ($url = $this->getNextNotCrawledUrl()) {
            $this->crawl($url);

            // check if the search has been deleted during the crawl process
            //if ($this->searchMustEnd()) {
            //    return false;
            //}
        }

        // this search is finished
        $this->search->update(['status' => Search::STATUS_FINISHED]);

        return false;
    }

    /**
     * Crawl an url and extract resources.
     *
     * @param mixed $url
     * @param bool $entryPoint
     */
    private function crawl($url, bool $entryPoint = false): void
    {
        $results = $this->extractor
            ->searchFor(['urls', 'emails'])
            ->at($entryPoint ? $url : $url->name)
            ->get();

        foreach (array_unique($results['urls']) as $item) {
            $item = $this->cleanUrl($item);

            if ($this->canBeStored($item) && $this->isValidUrl($item) && $this->isNotMediaFile($item) && $this->isInScope($item)) {
                Url::firstOrCreate(['name' => $item, 'search_id' => $this->search->id]);
            }
        }

        foreach (array_unique($results['emails']) as $email) {
            if ($this->canBeStored($email) && $this->isValidEmail($email)) {
                Email::firstOrCreate(['name' => $email, 'search_id' => $this->search->id]);
            }
        }

        if (! $entryPoint) {
            $url->update(['is_crawled' => true]);
        }
    }

    /**
     * Check if the search has been deleted or marked as finished.
     *
     * @return bool
     */
    private function searchMustEnd(): bool
    {
        return in_array($this->search->fresh()->status, [
            Search::STATUS_FINISHED,
            Search::STATUS_FAILED,
            Search::STATUS_PAUSED
        ]);
    }

    /**
     * Get the search's url that has not been crawled yet.
     *
     * @return \App\Url|null
     */
    private function getNextNotCrawledUrl(): ?Url
    {
        return $this->search->urls()
            ->notCrawled()
            ->first();
    }

    /**
     * A wrapper for php parse_url host function.
     *
     * @param string $url
     * @return string|null
     */
    private function getDomainName(string $url): ?string
    {
        return parse_url($url, PHP_URL_HOST) ?: null;
    }

    /**
     * A wrapper for php filter_var url function.
     *
     * @param string $url
     * @return bool
     */
    private function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * A wrapper for php filter_var email function.
     *
     * @param string $email
     * @return bool
     */
    private function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Remove unwanted character at the end of the url string,
     * and remove anchors in the url.
     *
     * @param string $url
     * @return string
     */
    private function cleanUrl(string $url): string
    {
        $url = rtrim(rtrim($url, '#'), '/');

        $fragment = parse_url($url, PHP_URL_FRAGMENT);

        if (! empty($fragment)) {
            $url = str_replace("#{$fragment}", '', $url);
        }

        return rtrim($url, '?');
    }

    /**
     * Whether a given url is a media file url.
     *
     * @param string $url
     * @return bool
     */
    private function isMediaFile(string $url): bool
    {
        return ends_with($url, [
            '.jpg', '.jp2', '.jpeg', '.raw', '.png', '.gif', '.tiff', '.bmp',
            '.svg', '.fla', '.swf', '.css', '.js', '.mp3', '.aac', '.wav',
            '.wma', '.aac', '.mp4', '.ogg', '.oga', '.ogv', '.flac', '.fla',
            '.ape', '.mpc', '.aif', '.aiff', '.m4a', '.mov', '.avi', '.wmv',
            '.qt', '.mp4a', '.mp4v', '.flv', '.ogm', '.mkv', '.mka', '.mks'
        ]);
    }

    /**
     * Whether a given url is a not media file url.
     *
     * @param string $url
     * @return bool
     */
    private function isNotMediaFile(string $url): bool
    {
        return ! $this->isMediaFile($url);
    }

    /**
     * Whether the given url in the scope of the current search.
     *
     * @param string $url
     * @return bool
     */
    private function isInScope(string $url): bool
    {
        if (! $this->search->is_limited) {
            return true;
        }

        return $this->getDomainName($url) === $this->getDomainName($this->search->url);
    }

    /**
     * Whether a value can be stored in a mysql varchar field.
     *
     * @param string $value
     * @return bool
     */
    private function canBeStored(string $value): bool
    {
        return strlen($value) <= 255;
    }
}

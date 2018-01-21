import client from './client'
import route from '../helpers/route'

export default {
  show: id => client.get(route('api.searches.statistics.show', id)),
}

import client from './client'
import route from '../helpers/route'

export default {
  show: id => client.get(route('api.searches.show', id)),
  update: data => client.put(route('api.searches.update', data.id), data),
  destroy: id => client.delete(route('api.searches.destroy', id))
}

import client from './client'
import route from '../helpers/route'

export default {
  index: (user, page) => client.get(route('api.users.searches.index', user.id), { params: { page } }),
  store: (user, data) => client.post(route('api.users.searches.store', user.id), data)
}

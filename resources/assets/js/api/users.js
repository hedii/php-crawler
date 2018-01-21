import client from './client'
import route from '../helpers/route'

export default {
  me: () => client.get(route('api.users.me')),
  update: user => client.put(route('api.users.update', user.id), user)
}

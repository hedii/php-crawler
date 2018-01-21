import Dashboard from './../components/Dashboard'
import SearchIndex from './../components/search/SearchIndex'
import SearchShow from './../components/search/SearchShow'
import SearchCreate from './../components/search/SearchCreate'
import UserEdit from './../components/user/UserEdit'

export default [
  {
    path: '/',
    name: 'dashboard',
    component: Dashboard
  }, {
    path: `/searches`,
    name: 'searches.index',
    component: SearchIndex
  }, {
    path: `/searches/create`,
    name: 'searches.create',
    component: SearchCreate
  }, {
    path: `/searches/:searchId`,
    name: 'searches.show',
    component: SearchShow
  }, {
    path: `/account`,
    name: 'account.edit',
    component: UserEdit
  }
]

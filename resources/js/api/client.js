import axios from 'axios'

export default axios.create({
  baseURL: window.apiUrl,
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content
  }
})

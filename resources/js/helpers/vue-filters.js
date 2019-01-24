import Vue from 'vue'

Vue.filter('truncate', (value, length, clamp = '...') => {
  if (!value) return ''
  value = value.toString()
  let node = document.createElement('div')
  node.innerHTML = value
  let content = node.textContent
  return content.length > length ? content.slice(0, length) + clamp : content
})

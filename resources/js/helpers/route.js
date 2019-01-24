export default function () {
  let routes = Object.assign(window.apiRoutes)
  let args = Array.prototype.slice.call(arguments)
  let name = args.shift()

  if (routes[name] === undefined) {
    console.error('Unknown route ', name);
    return false
  }

  return routes[name]
    .split('/')
    .map(s => s[0] == '{' ? args.shift() : s)
    .join('/')
}

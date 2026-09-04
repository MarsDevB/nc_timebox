/**
 * Nextcloud - TimeBox personal settings
 */
(function () {
function init() {
var saveBtn = document.getElementById('timebox-settings-save')
var status = document.getElementById('timebox-settings-status')
if (!saveBtn) return
saveBtn.addEventListener('click', function () {
var maxItems = parseInt(document.getElementById('timebox-max-items').value, 10) || 50
var maxEvents = parseInt(document.getElementById('timebox-max-events').value, 10) || 50
var url = OC.generateUrl('/apps/timebox/settings')
var xhr = new XMLHttpRequest()
xhr.open('POST', url, true)
xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
xhr.setRequestHeader('requesttoken', OC.requestToken)
xhr.onload = function () {
status.textContent = (xhr.status >= 200 && xhr.status < 300) ? '✓' : '✗'
setTimeout(function () { status.textContent = '' }, 2000)
}
xhr.send('maxItems=' + encodeURIComponent(maxItems) + '&maxEvents=' + encodeURIComponent(maxEvents))
})
}
if (document.readyState === 'loading') {
document.addEventListener('DOMContentLoaded', init)
} else {
init()
}
})()

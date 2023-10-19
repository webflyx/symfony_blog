import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// Hamburget Menu
const menu = document.getElementById('main-menu')
document.getElementById('hamburger').addEventListener('click', () => {
    if (menu.classList.contains('hidden')) {
        menu.classList.remove('hidden');
    } else {
        menu.classList.add('hidden');
    }
})

// Show / Hide Alerts
function showAlert($id, $time, data = '') {
    document.getElementById('notification-content').innerHTML = data;
    document.getElementById($id).classList.remove('invisible', 'opacity-0');
    setTimeout(() => {
        document.getElementById($id).classList.add('invisible', 'opacity-0');
    }, $time);
}

// Notification
document.getElementById('notification-close').addEventListener('click', () => {
    showAlert('notification', 0);
})

//Profile
const profileTabs = document.querySelectorAll('.profile-tab');
const profileContent = document.querySelectorAll('.profile-content');
profileTabs.forEach(el => {
    el.addEventListener('click', () => {
        var thisTab = el.getAttribute('id');
        var thisContent = thisTab.replace('tab-', '');
        console.log(thisContent);

        profileTabs.forEach(elTab => {
            elTab.classList.remove('border-b-2', 'border-sky-500', 'text-sky-500');
        })
        profileContent.forEach(elContent => {
            elContent.classList.add('hidden');
        })

        el.classList.add('border-b-2', 'border-sky-500', 'text-sky-500');
        document.getElementById(thisContent).classList.remove('hidden');

    })
});


var pusher = new Pusher('b4acbf76e7cae651140b', {cluster: 'eu'});
var channel = pusher.subscribe('symfony-blog');
channel.bind('new-post-event', function (data) {
    showAlert('notification', 10000, data);
});


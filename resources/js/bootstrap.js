import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import './autobahn.js';
window.ab = ab;

// window.conn = new ab.Session('ws://localhost:8080',
//     function() {
//         conn.subscribe('kittensCategory', function(topic, data) {
//             // This is where you would add the new article to the DOM (beyond the scope of this tutorial)
//             console.log('New article published to category "' + topic + '" : ' + data.title);
//         });
//     },
//     function() {
//         console.warn('WebSocket connection closed');
//     },
//     {'skipSubprotocolCheck': true}
// );

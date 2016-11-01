Vue.component('torshot-frames', {
    template: '#frames-template',
})
new Vue({
    el: '#torshot-frames-container',
    data: {
        frames: [
            'http://placehold.it/400',
            'http://placehold.it/400',
            'http://placehold.it/400'
        ]
    }
})
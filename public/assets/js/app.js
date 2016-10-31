Vue.component('torshot-frame', {
    template: '#frame-template',
    props: ['frame'],
    data: function () {
        return {
            frames: [
                {src: 'http://placehold.it/400'},
                {src: 'http://placehold.it/400'}
            ]
        }
    }
});

new Vue( {
    el: '#app'
});
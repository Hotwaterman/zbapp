Component({
    properties: {
        item: {
            type: Object,
            default() {
                return {}
            }
        }
    },
    watch: {
        item: {
            handler(val) {
                console.log('val', val)
            },
            deep: true
        }
    },
    data: {
        pageMax: 1,
        pageInput: '',
        showPageInput: false
    },
    methods: {
        previewImage(e) {
            const image = e.currentTarget.dataset.image
            this.triggerEvent('preview', image)
        },
        retry(e) {
            const draw_id = e.currentTarget.dataset.id
            this.triggerEvent('retry', draw_id)
        }
    }
})

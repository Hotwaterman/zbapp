Component({
    properties: {
        text: {
            type: String,
            default: ''
        }
    },
    methods: {
        onCopySuccess() {
            app.util.message('已复制')
        },
        onCopyError() {
            app.util.message('已复制', 'error')
        },
        trim(str) {
            return str.replace(/(^\s*)|(\s*$)/g, '');
        }
    }
})

import Alpine from "alpinejs";

Alpine.data('reviews', () => ({
    visible: 9,
    hasMore: true,

    handleMore() {
        this.visible += 9;
        if (this.visible === 36) return this.finish();
    },

    isVisible(index) {
        return index < this.visible;
    },

    finish() {
        this.hasMore = false;
    }
}));

Alpine.data('review', () => ({
    expanded: false,
    isClamped: false,

    async init() {
        await this.$nextTick();
        const el = this.$refs.content;
        this.isClamped = el.scrollHeight > el.clientHeight;
    }
}));

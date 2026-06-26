// BS date input mask — digits only, auto-hyphens: YYYY-MM-DD
function bsDateInput(initVal) {
    return {
        fmt(raw) {
            const d = (raw || '').replace(/\D/g, '').slice(0, 8);
            if (d.length > 6) return d.slice(0,4) + '-' + d.slice(4,6) + '-' + d.slice(6);
            if (d.length > 4) return d.slice(0,4) + '-' + d.slice(4);
            return d;
        },

        // beforeinput fires BEFORE the character appears — most reliable block point.
        onBeforeInput(e) {
            if (e.data && /\D/.test(e.data)) e.preventDefault();
        },

        // Safety net: strip any non-digit that still slipped through.
        onInput(e) {
            const next = this.fmt(e.target.value);
            if (e.target.value !== next) e.target.value = next;
        },

        onPaste(e) {
            const next = this.fmt(e.clipboardData?.getData('text') || '');
            e.target.value = next;
        },

        init() {
            // Set initial value directly on the DOM element.
            this.$nextTick(() => {
                if (this.$refs.bs) this.$refs.bs.value = this.fmt(initVal || '');
            });
        },
    };
}

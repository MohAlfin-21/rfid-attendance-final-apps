import './bootstrap';

import Alpine from 'alpinejs';

document.addEventListener('alpine:init', () => {
    Alpine.data('liveClock', (timezone = 'Asia/Jakarta', locale = 'id-ID') => ({
        timezone,
        locale,
        timeText: '--:--:--',
        dateText: '',

        init() {
            this.updateClock();
            this.timer = window.setInterval(() => this.updateClock(), 1000);
        },

        updateClock() {
            const now = new Date();

            try {
                this.timeText = new Intl.DateTimeFormat(this.locale, {
                    timeZone: this.timezone,
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false,
                }).format(now);

                this.dateText = new Intl.DateTimeFormat(this.locale, {
                    timeZone: this.timezone,
                    weekday: 'short',
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                }).format(now);
            } catch (error) {
                this.timeText = now.toLocaleTimeString();
                this.dateText = now.toLocaleDateString();
            }
        },
    }));
});

window.Alpine = Alpine;

Alpine.start();

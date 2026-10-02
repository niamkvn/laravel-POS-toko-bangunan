import * as bootstrap from 'bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.bootstrap = bootstrap;
window.Chart = Chart;
window.Alpine = Alpine;

const MONTHS_ID = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
const DAYS_ID = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

window.nbDatePicker = function (initialValue) {
    return {
        open: false,
        alignRight: false,
        value: initialValue || '',
        viewYear: null,
        viewMonth: null,
        days: [],
        dayNames: DAYS_ID,
        init() {
            const base = this.value ? new Date(this.value + 'T00:00:00') : new Date();
            this.viewYear = base.getFullYear();
            this.viewMonth = base.getMonth();
            this.buildDays();
        },
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => {
                    const rect = this.$el.getBoundingClientRect();
                    const panelWidth = 280;
                    this.alignRight = (rect.left + panelWidth) > window.innerWidth - 12;
                });
            }
        },
        get label() {
            if (!this.value) return 'Pilih tanggal';
            const [y, m, d] = this.value.split('-').map(Number);
            if (!y || !m || !d) return 'Pilih tanggal';
            return `${d} ${MONTHS_ID[m - 1]} ${y}`;
        },
        get monthLabel() {
            return `${MONTHS_ID[this.viewMonth]} ${this.viewYear}`;
        },
        buildDays() {
            const firstDay = new Date(this.viewYear, this.viewMonth, 1);
            const startOffset = firstDay.getDay();
            const daysInMonth = new Date(this.viewYear, this.viewMonth + 1, 0).getDate();
            const days = [];
            for (let i = 0; i < startOffset; i++) days.push(null);
            for (let d = 1; d <= daysInMonth; d++) days.push(d);
            this.days = days;
        },
        prevMonth() {
            this.viewMonth--;
            if (this.viewMonth < 0) { this.viewMonth = 11; this.viewYear--; }
            this.buildDays();
        },
        nextMonth() {
            this.viewMonth++;
            if (this.viewMonth > 11) { this.viewMonth = 0; this.viewYear++; }
            this.buildDays();
        },
        selectDay(d) {
            if (!d) return;
            const mm = String(this.viewMonth + 1).padStart(2, '0');
            const dd = String(d).padStart(2, '0');
            this.value = `${this.viewYear}-${mm}-${dd}`;
            this.open = false;
            this.$dispatch('change');
        },
        clear() {
            this.value = '';
            this.open = false;
        },
        goToday() {
            const t = new Date();
            this.viewYear = t.getFullYear();
            this.viewMonth = t.getMonth();
            this.buildDays();
            this.selectDay(t.getDate());
        },
        isSelected(d) {
            if (!d || !this.value) return false;
            const [y, m, day] = this.value.split('-').map(Number);
            return y === this.viewYear && m === this.viewMonth + 1 && day === d;
        },
        isToday(d) {
            if (!d) return false;
            const t = new Date();
            return t.getFullYear() === this.viewYear && t.getMonth() === this.viewMonth && t.getDate() === d;
        },
    };
};

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
        new bootstrap.Tooltip(el);
    });
});

/**
 * SearchableSelect — wraps a native <select> with a custom searchable dropdown.
 *
 * Usage:
 *   Add data-searchable to any <select> for auto-init on DOMContentLoaded.
 *   Or call: new SearchableSelect(selectElement)
 *   Or call: window.initSearchableSelects(containerEl) to init all selects inside a container.
 *
 * Features:
 *  - Native <select> stays in DOM (hidden) — form submissions work correctly.
 *  - Menu is appended to <body> with position:fixed — escapes overflow:hidden ancestors.
 *  - Smart positioning: opens upward when there is insufficient space below.
 *  - MutationObserver rebuilds options when the native select's options change dynamically.
 *  - Dispatches a native 'change' event so onchange="" handlers still fire.
 *  - Removes sibling arrow overlays (old $arr pattern) left by previous markup.
 */

// Track the currently open instance so we can close it when another opens
let _currentOpen = null;

class SearchableSelect {
    constructor(select) {
        if (select._ss) return; // already initialized
        select._ss = this;
        this.select  = select;
        this._rafId  = null;
        this._build();
        this._bindEvents();
        this._observe();
        this._syncLabel();
    }

    // ── Build DOM ──────────────────────────────────────────────────────────

    _build() {
        const s = this.select;
        const cls = s.className.split(/\s+/);

        // Extract sizing/shape from native select so the button matches exactly
        const pyClass      = cls.find(c => /^py-/.test(c))             || 'py-2.5';
        const textSizeCls  = cls.find(c => /^text-(xs|sm|base|lg)$/.test(c)) || 'text-sm';
        const roundedCls   = cls.find(c => /^rounded/.test(c))         || 'rounded-xl';

        // Extract layout classes (width, flex) to preserve sizing in wrapper
        const layoutCls = cls
            .filter(c => /^(?:(?:sm|md|lg|xl|2xl):)?(?:w-|min-w-|max-w-|flex-[0-9]|grow|shrink)/.test(c))
            .join(' ');

        // Wrapper (stays inline where the select was)
        this.wrapper = document.createElement('div');
        this.wrapper.className = ('ss-wrap relative ' + (layoutCls || 'w-full')).trim();
        this.wrapper._ss = this;
        s.parentNode.insertBefore(this.wrapper, s);
        this.wrapper.appendChild(s);
        s.style.cssText = 'display:none!important;position:absolute;width:0;height:0';

        // Remove any sibling arrow overlay left by the old $arr / inline-arrow pattern
        // (a pointer-events-none absolute div that was the visual chevron for the native select)
        Array.from(this.wrapper.parentNode.children).forEach(child => {
            if (child !== this.wrapper &&
                child.classList.contains('pointer-events-none') &&
                child.classList.contains('absolute')) {
                child.remove();
            }
        });

        // Trigger button — inherits py, text-size, rounded from original select
        this.btn = document.createElement('button');
        this.btn.type = 'button';
        this.btn.className = [
            'w-full flex items-center justify-between gap-2',
            `px-3 ${pyClass} border border-slate-200 ${roundedCls}`,
            `${textSizeCls} bg-white text-left transition-all cursor-pointer`,
            'focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400',
        ].join(' ');

        this.labelSpan = document.createElement('span');
        this.labelSpan.className = `truncate flex-1 ${textSizeCls}`;
        this._textSizeCls = textSizeCls;

        const arrowSize = textSizeCls === 'text-xs' ? 'w-3 h-3' : 'w-4 h-4';
        const arrow = document.createElement('span');
        arrow.className = 'flex-shrink-0';
        arrow.innerHTML = `<svg class="${arrowSize} text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>`;

        this.btn.appendChild(this.labelSpan);
        this.btn.appendChild(arrow);
        this.wrapper.appendChild(this.btn);

        // Dropdown menu — appended to <body> with position:fixed to escape overflow:hidden ancestors
        this.menu = document.createElement('div');
        this.menu.className = [
            'ss-menu fixed z-[9999]',
            `bg-white ${roundedCls} border border-slate-200`,
            'shadow-xl shadow-slate-200/60',
        ].join(' ');
        this.menu.style.display = 'none';
        this.menu._ss = this;
        document.body.appendChild(this.menu);

        // Search input (hidden when ≤3 options — no point searching)
        this.searchInput = document.createElement('input');
        this.searchInput.type = 'text';
        this.searchInput.placeholder = 'ค้นหา...';
        this.searchInput.className = [
            'w-full px-3 py-2 text-sm',
            'border-b border-slate-100 bg-white',
            'focus:outline-none placeholder-slate-400',
            'rounded-t-xl',
        ].join(' ');

        // Options container
        this.optsList = document.createElement('div');
        this.optsList.className = 'ss-opts max-h-48 overflow-y-auto py-1';

        this.menu.appendChild(this.searchInput);
        this.menu.appendChild(this.optsList);

        this._populate();
    }

    // ── Populate option buttons from native <select> ───────────────────────

    _populate() {
        const opts = Array.from(this.select.options);
        this.optsList.innerHTML = '';

        opts.forEach(opt => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.dataset.value = opt.value;
            btn.dataset.label = opt.text;
            btn.textContent   = opt.text;
            btn.style.cssText = 'width:calc(100% - 8px);margin-left:4px';

            btn.className = opt.value
                ? 'w-full text-left px-3 py-2 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition-colors cursor-pointer'
                : 'w-full text-left px-3 py-2 text-sm text-slate-400 hover:bg-slate-50 rounded-lg transition-colors cursor-pointer';

            btn.addEventListener('click', () => {
                this.select.value = opt.value;
                this.select.dispatchEvent(new Event('change', { bubbles: true }));
                this._syncLabel();
                this._close();
            });

            this.optsList.appendChild(btn);
        });

        // Hide search input when there are very few options
        this.searchInput.style.display = opts.length > 3 ? '' : 'none';
    }

    // ── Sync displayed label to current select value ───────────────────────

    _getPlaceholder() {
        const first = this.select.options[0];
        return (first && !first.value) ? first.text : (this.select.dataset.placeholder || '— เลือก —');
    }

    _syncLabel() {
        const sel = this.select;
        const idx = sel.selectedIndex;
        const opt = idx >= 0 ? sel.options[idx] : null;

        if (opt && opt.value) {
            this.labelSpan.textContent = opt.text;
            this.labelSpan.className   = 'truncate flex-1 text-slate-700';
        } else {
            this.labelSpan.textContent = this._getPlaceholder();
            this.labelSpan.className   = 'truncate flex-1 text-slate-400';
        }
    }

    // ── Events ─────────────────────────────────────────────────────────────

    _bindEvents() {
        this.btn.addEventListener('click', e => {
            e.stopPropagation();
            this.menu.style.display === 'none' ? this._open() : this._close();
        });

        // Filter options on search
        this.searchInput.addEventListener('input', () => {
            const q = this.searchInput.value.toLowerCase();
            this.optsList.querySelectorAll('button').forEach(btn => {
                btn.style.display = btn.dataset.label.toLowerCase().includes(q) ? '' : 'none';
            });
        });

        // Close on outside click (captures clicks on body including other SS menus)
        document.addEventListener('click', e => {
            if (this.menu.style.display === 'none') return;
            if (!this.wrapper.contains(e.target) && !this.menu.contains(e.target)) {
                this._close();
            }
        });

        // Close on Escape
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && this.menu.style.display !== 'none') {
                this._close();
                this.btn.focus();
            }
        });

        // Close on scroll outside the menu (page scroll), but NOT when scrolling inside the options list
        const onScroll = (e) => {
            if (this.menu.style.display === 'none') return;
            if (e.target && this.menu.contains(e.target)) return; // scrolling inside menu — allow
            this._close();
        };
        const onResize = () => { if (this.menu.style.display !== 'none') this._close(); };
        window.addEventListener('scroll', onScroll, { passive: true, capture: true });
        window.addEventListener('resize', onResize, { passive: true });
    }

    // ── Smart positioning using getBoundingClientRect (fixed coords) ───────

    _open() {
        // Close the previous open dropdown
        if (_currentOpen && _currentOpen !== this) _currentOpen._close();
        _currentOpen = this;

        // Temporarily show with visibility:hidden to measure menu height
        this.menu.style.visibility = 'hidden';
        this.menu.style.display = '';
        this.menu.style.width = this.wrapper.offsetWidth + 'px';

        const btnRect = this.btn.getBoundingClientRect();
        const menuH   = this.menu.offsetHeight;
        const spaceBelow = window.innerHeight - btnRect.bottom;

        if (spaceBelow < menuH + 8 && btnRect.top > spaceBelow) {
            // Not enough room below — open upward
            this.menu.style.top    = 'auto';
            this.menu.style.bottom = (window.innerHeight - btnRect.top + 4) + 'px';
        } else {
            // Default — open downward
            this.menu.style.top    = (btnRect.bottom + 4) + 'px';
            this.menu.style.bottom = 'auto';
        }

        this.menu.style.left       = btnRect.left + 'px';
        this.menu.style.visibility = '';

        // Reset search filter
        this.searchInput.value = '';
        this.optsList.querySelectorAll('button').forEach(b => b.style.display = '');

        if (this.searchInput.style.display !== 'none') {
            this.searchInput.focus();
        }
    }

    _close() {
        this.menu.style.display = 'none';
        if (_currentOpen === this) _currentOpen = null;
    }

    // ── MutationObserver — rebuild when options change dynamically ─────────

    _observe() {
        this._observer = new MutationObserver(() => {
            cancelAnimationFrame(this._rafId);
            this._rafId = requestAnimationFrame(() => {
                this._populate();
                this._syncLabel();
            });
        });
        this._observer.observe(this.select, { childList: true });
    }

    // ── Public API ─────────────────────────────────────────────────────────

    refresh() {
        this._populate();
        this._syncLabel();
    }

    setValue(value) {
        this.select.value = value;
        this._syncLabel();
    }
}

// Auto-initialize all [data-searchable] selects on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('select[data-searchable]').forEach(sel => new SearchableSelect(sel));
});

/**
 * Initialize SearchableSelect on all <select> elements inside a container.
 * Call this after dynamically inserting HTML into the DOM.
 * @param {Element} container
 */
window.initSearchableSelects = function (container) {
    (container || document).querySelectorAll('select').forEach(function (sel) {
        if (!sel._ss) new SearchableSelect(sel);
    });
};

window.SearchableSelect = SearchableSelect;
export default SearchableSelect;

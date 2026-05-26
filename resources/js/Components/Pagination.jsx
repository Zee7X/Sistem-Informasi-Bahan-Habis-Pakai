import { Link } from '@inertiajs/react';

export default function Pagination({ pagination }) {
    if (!pagination) return null;

    // Handle both wrapped (.meta) and raw paginator objects
    const data = pagination.meta ? pagination.meta : pagination;
    const links = pagination.links || pagination.meta?.links;
    const lastPage = data.last_page;
    const from = data.from;
    const to = data.to;
    const total = data.total;

    if (!lastPage || total === 0) return null;

    const cleanLabel = (label) => {
        if (label.includes('Previous') || label.includes('Sebelumnya') || label.includes('laquo')) return 'Previous';
        if (label.includes('Next') || label.includes('Berikutnya') || label.includes('raquo')) return 'Next';
        return label;
    };

    return (
        <div className="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 border-t border-border bg-dark-card">
            <span className="text-xs text-text-secondary">
                Menampilkan {from ?? 0}–{to ?? 0} dari {total ?? 0} entri
            </span>
            <div className="flex items-center gap-1.5">
                {links?.map((link, idx) => {
                    const label = cleanLabel(link.label);
                    const isNav = label === 'Previous' || label === 'Next';
                    
                    if (!link.url) {
                        return (
                            <span
                                key={idx}
                                className={`flex items-center justify-center rounded text-xs font-medium select-none border border-border bg-dark-card text-text-secondary/40 cursor-not-allowed ${
                                    isNav ? 'px-3 h-8' : 'w-8 h-8'
                                }`}
                            >
                                {label}
                            </span>
                        );
                    }

                    return (
                        <Link
                            key={idx}
                            href={link.url}
                            className={`flex items-center justify-center rounded text-xs font-medium transition-colors ${
                                isNav
                                    ? 'px-3 h-8 border border-border bg-dark-card text-text-primary hover:bg-dark-surface hover:text-text-primary'
                                    : link.active
                                    ? 'w-8 h-8 bg-violet text-white'
                                    : 'w-8 h-8 text-text-secondary hover:bg-dark-surface hover:text-text-primary'
                            }`}
                            preserveScroll
                        >
                            {label}
                        </Link>
                    );
                })}
            </div>
        </div>
    );
}

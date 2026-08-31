import { useState, useRef, useEffect } from 'react';
import { ChevronDown, Search, X } from 'lucide-react';

export default function SearchableSelect({
    options = [],
    value = '',
    onChange,
    placeholder = 'Pilih...',
    nameKey = 'nama_bahan',
    specKey = 'spesifikasi',
    emptyMessage = 'Tidak ditemukan hasil',
    required = false,
    className = '',
    renderExtra = null, // Callback to render extra info (e.g. stock)
}) {
    const [isOpen, setIsOpen] = useState(false);
    const [searchQuery, setSearchQuery] = useState('');
    const containerRef = useRef(null);
    const searchInputRef = useRef(null);

    // Find the currently selected option
    const selectedOption = options.find(opt => String(opt.id) === String(value));

    // Filter options based on query (search in nameKey and specKey)
    const filteredOptions = options.filter(opt => {
        const name = String(opt[nameKey] || '').toLowerCase();
        const spec = String(opt[specKey] || '').toLowerCase();
        const query = searchQuery.toLowerCase();
        return name.includes(query) || spec.includes(query);
    });

    useEffect(() => {
        function handleClickOutside(event) {
            if (containerRef.current && !containerRef.current.contains(event.target)) {
                setIsOpen(false);
            }
        }
        if (isOpen) {
            document.addEventListener('mousedown', handleClickOutside);
            // Focus search input when opened
            setTimeout(() => {
                searchInputRef.current?.focus();
            }, 50);
        }
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, [isOpen]);

    // Handle selection
    const handleSelect = (option) => {
        onChange(option.id);
        setIsOpen(false);
        setSearchQuery('');
    };

    return (
        <div className={`relative ${className}`} ref={containerRef}>
            {/* The trigger button */}
            <div
                onClick={() => setIsOpen(!isOpen)}
                className="input flex items-center justify-between cursor-pointer select-none py-1.5 h-auto min-h-[32px]"
            >
                {selectedOption ? (
                    <div className="flex flex-col py-0.5 leading-tight">
                        <span className="text-sm font-medium text-text-primary">
                            {selectedOption[nameKey]}
                        </span>
                        {selectedOption[specKey] && (
                            <span className="text-2xs text-text-secondary mt-0.5">
                                {selectedOption[specKey]}
                            </span>
                        )}
                    </div>
                ) : (
                    <span className="text-text-secondary text-sm">{placeholder}</span>
                )}
                <ChevronDown size={14} className={`text-text-secondary transition-transform flex-shrink-0 ml-2 ${isOpen ? 'rotate-180' : ''}`} />
            </div>

            {/* Hidden input for HTML form validation if required */}
            <input
                type="text"
                className="sr-only"
                value={value}
                onChange={() => {}}
                required={required}
                tabIndex={-1}
            />

            {/* The Dropdown Panel */}
            {isOpen && (
                <div className="absolute left-0 right-0 z-50 mt-1 bg-dark-card border border-border rounded shadow-modal max-h-72 flex flex-col overflow-hidden">
                    {/* Search Field */}
                    <div className="p-2 border-b border-border/80 flex items-center gap-2 bg-dark-surface/30">
                        <Search size={14} className="text-text-secondary flex-shrink-0" />
                        <input
                            ref={searchInputRef}
                            type="text"
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            placeholder="Cari berdasarkan nama atau spesifikasi..."
                            className="bg-transparent text-xs w-full focus:outline-none text-text-primary placeholder:text-text-secondary/60"
                        />
                        {searchQuery && (
                            <button
                                type="button"
                                onClick={() => setSearchQuery('')}
                                className="text-text-secondary hover:text-text-primary flex-shrink-0"
                            >
                                <X size={14} />
                            </button>
                        )}
                    </div>

                    {/* Options List */}
                    <div className="overflow-y-auto max-h-56 divide-y divide-border/30">
                        {filteredOptions.length > 0 ? (
                            filteredOptions.map((option) => {
                                const isSelected = String(option.id) === String(value);
                                return (
                                    <div
                                        key={option.id}
                                        onClick={() => handleSelect(option)}
                                        className={`flex flex-col p-2.5 cursor-pointer text-left transition-colors ${
                                            isSelected
                                                ? 'bg-violet/10 border-l-2 border-l-violet'
                                                : 'hover:bg-dark-surface/50'
                                        }`}
                                    >
                                        <div className="flex items-center justify-between">
                                            <span className="text-xs font-semibold text-text-primary">
                                                {option[nameKey]}
                                            </span>
                                            {renderExtra && (
                                                <span className="text-2xs text-text-secondary font-medium">
                                                    {renderExtra(option)}
                                                </span>
                                            )}
                                        </div>
                                        {option[specKey] && (
                                            <span className="text-2xs text-text-secondary mt-0.5">
                                                {option[specKey]}
                                            </span>
                                        )}
                                    </div>
                                );
                            })
                        ) : (
                            <div className="p-4 text-center text-xs text-text-secondary">
                                {emptyMessage}
                            </div>
                        )}
                    </div>
                </div>
            )}
        </div>
    );
}

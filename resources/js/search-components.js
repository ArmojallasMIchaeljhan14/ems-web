/**
 * Universal Search Component for Index Pages
 * Provides consistent search functionality across all admin and user index pages
 */

// Generic Search Function
function createSearchComponent(options = {}) {
    return {
        search: '',
        filteredItems: [],
        
        // Initialize with data
        init() {
            this.items = options.items || [];
            this.filteredItems = [...this.items];
            
            // Set initial filter values
            this.filters = options.filters || {};
            Object.keys(this.filters).forEach(key => {
                this.filters[key] = '';
            });
        },
        
        // Filter items based on search and filters
        filterItems() {
            let filtered = [...this.items];
            
            // Text search
            if (this.search) {
                const searchTerm = this.search.toLowerCase();
                filtered = filtered.filter(item => {
                    return options.searchFields.some(field => {
                        const value = this.getNestedValue(item, field);
                        return value && value.toString().toLowerCase().includes(searchTerm);
                    });
                });
            }
            
            // Apply additional filters
            Object.keys(this.filters).forEach(filterKey => {
                const filterValue = this.filters[filterKey];
                if (filterValue) {
                    filtered = filtered.filter(item => {
                        return options.filterFunctions[filterKey](item, filterValue);
                    });
                }
            });
            
            this.filteredItems = filtered;
        },
        
        // Clear all filters
        clearFilters() {
            this.search = '';
            Object.keys(this.filters).forEach(key => {
                this.filters[key] = '';
            });
            this.filterItems();
        },
        
        // Helper to get nested object values
        getNestedValue(obj, path) {
            return path.split('.').reduce((current, key) => {
                return current && current[key] !== undefined ? current[key] : null;
            }, obj);
        }
    };
}

// Event Search Component
function eventIndex() {
    return createSearchComponent({
        searchFields: ['title', 'description', 'venue.name'],
        filters: {
            status: '',
            dateFilter: ''
        },
        filterFunctions: {
            status: (item, value) => item.status === value,
            dateFilter: (item, value) => {
                const now = new Date();
                const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                const thisWeekStart = new Date(today);
                thisWeekStart.setDate(today.getDate() - today.getDay());
                const thisWeekEnd = new Date(thisWeekStart);
                thisWeekEnd.setDate(thisWeekStart.getDate() + 6);
                const thisMonthStart = new Date(now.getFullYear(), now.getMonth(), 1);
                const thisMonthEnd = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                const eventDate = new Date(item.start_at);
                
                switch(value) {
                    case 'upcoming':
                        return eventDate >= today;
                    case 'today':
                        return eventDate.toDateString() === today.toDateString();
                    case 'this_week':
                        return eventDate >= thisWeekStart && eventDate <= thisWeekEnd;
                    case 'this_month':
                        return eventDate >= thisMonthStart && eventDate <= thisMonthEnd;
                    case 'past':
                        return eventDate < today;
                    default:
                        return true;
                }
            }
        }
    });
}

// Multimedia Search Component
function multimediaIndex() {
    return createSearchComponent({
        searchFields: ['caption', 'event.title', 'user.name'],
        filters: {
            typeFilter: '',
            mediaFilter: ''
        },
        filterFunctions: {
            typeFilter: (item, value) => item.type === value,
            mediaFilter: (item, value) => {
                const mediaCount = item.media ? item.media.length : 0;
                const hasImages = item.media ? item.media.some(m => m.type === 'image') : false;
                const hasVideos = item.media ? item.media.some(m => m.type === 'video') : false;
                
                switch(value) {
                    case 'with_media':
                        return mediaCount > 0;
                    case 'images_only':
                        return hasImages && !hasVideos;
                    case 'videos_only':
                        return hasVideos;
                    case 'no_media':
                        return mediaCount === 0;
                    default:
                        return true;
                }
            }
        }
    });
}

// User Search Component
function userSearch() {
    return createSearchComponent({
        searchFields: ['name', 'email', 'role'],
        filters: {
            roleFilter: '',
            statusFilter: ''
        },
        filterFunctions: {
            roleFilter: (item, value) => item.role === value,
            statusFilter: (item, value) => item.status === value
        }
    });
}

// Venue Search Component
function venueSearch() {
    return createSearchComponent({
        searchFields: ['name', 'address', 'facilities'],
        filters: {
            capacityFilter: ''
        },
        filterFunctions: {
            capacityFilter: (item, value) => {
                const capacity = parseInt(item.capacity);
                switch(value) {
                    case 'small':
                        return capacity >= 1 && capacity <= 50;
                    case 'medium':
                        return capacity >= 51 && capacity <= 200;
                    case 'large':
                        return capacity >= 201 && capacity <= 500;
                    case 'xlarge':
                        return capacity > 500;
                    default:
                        return true;
                }
            }
        }
    });
}

// Participant Search Component
function participantSearch() {
    return createSearchComponent({
        searchFields: ['name', 'email', 'event.title', 'department'],
        filters: {
            statusFilter: '',
            eventFilter: ''
        },
        filterFunctions: {
            statusFilter: (item, value) => item.status === value,
            eventFilter: (item, value) => item.event && item.event.title.toLowerCase().includes(value.toLowerCase())
        }
    });
}

// Document Search Component
function documentSearch() {
    return createSearchComponent({
        searchFields: ['title', 'description', 'type'],
        filters: {
            typeFilter: '',
            dateFilter: ''
        },
        filterFunctions: {
            typeFilter: (item, value) => item.type === value,
            dateFilter: (item, value) => {
                const now = new Date();
                const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                const docDate = new Date(item.created_at);
                
                switch(value) {
                    case 'today':
                        return docDate.toDateString() === today.toDateString();
                    case 'this_week':
                        const weekAgo = new Date(today);
                        weekAgo.setDate(today.getDate() - 7);
                        return docDate >= weekAgo;
                    case 'this_month':
                        const monthAgo = new Date(today);
                        monthAgo.setMonth(today.getMonth() - 1);
                        return docDate >= monthAgo;
                    default:
                        return true;
                }
            }
        }
    });
}

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        createSearchComponent,
        eventIndex,
        multimediaIndex,
        userSearch,
        venueSearch,
        participantSearch,
        documentSearch
    };
}

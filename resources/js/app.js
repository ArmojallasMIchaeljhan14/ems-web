import './bootstrap';

import Alpine from 'alpinejs';
import * as FullCalendar from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

window.Alpine = Alpine;
window.FullCalendar = { FullCalendar, dayGridPlugin, timeGridPlugin, interactionPlugin };

Alpine.start();

/**
 * Mock data for the design preview workflow (`/preview`).
 *
 * These functions mirror the shapes produced by
 * `app/Services/ScreenDataGenerator.php` so that signage designs render with
 * realistic data WITHOUT a database, screens, playlists or websocket server.
 *
 * Everything is plain JS and deterministic-ish (times are computed relative to
 * `new Date()` so the schedule always looks "live"). Tweak freely while
 * developing a design — nothing here touches the backend.
 */

// Anchor everything to "now" so previews always look current.
const NOW = new Date();

/** Build an ISO string offset from now by a number of minutes. */
function iso(offsetMinutes = 0) {
    return new Date(NOW.getTime() + offsetMinutes * 60_000).toISOString();
}

/** Build the `pivot` object every room carries (see Screen::rooms withPivot). */
function pivot(overrides = {}) {
    return {
        sort: 0,
        rotation: 0,
        mirror: false,
        icon: null,
        flags: null,
        starts_at: null,
        ends_at: null,
        ...overrides,
    };
}

export function mockRooms() {
    return [
        {
            id: 1,
            name: "Main Stage",
            venue_name: "Main Stage",
            pivot: pivot({ sort: 1, icon: "arrow-up", flags: "wheelchair" }),
        },
        {
            id: 2,
            name: "Workshop Room A",
            venue_name: "Workshop Room A",
            pivot: pivot({ sort: 2, icon: "arrow-right", flags: "first_aid" }),
        },
        {
            id: 3,
            name: "Workshop Room B",
            venue_name: "Workshop Room B",
            pivot: pivot({ sort: 3, icon: "arrow-left" }),
        },
        {
            id: 4,
            name: "Art Gallery",
            venue_name: "Art Gallery",
            pivot: pivot({ sort: 4, icon: "arrow-up", rotation: 90 }),
        },
        {
            id: 5,
            name: "Dealers Den",
            venue_name: "Dealers Den",
            pivot: pivot({ sort: 5, icon: "arrow-down", flags: "wheelchair,first_aid" }),
        },
    ];
}

export function mockScreen() {
    return {
        id: 1,
        name: "Preview Screen",
        slug: "preview-screen",
        hostname: "preview.local",
        orientation: "landscape",
        version: 1,
        status: "online",
        playlist_id: 1,
        screen_group_id: null,
        room_id: 1,
        room: mockRooms()[0],
        rooms: mockRooms(),
    };
}

/**
 * A schedule with 8-12 entries spread across "today", a couple of which are
 * delayed. Each entry matches the ScheduleEntry shape consumed by designs:
 * `room`, `scheduleType`, `scheduleOrganizer`, `starts_at`, `ends_at`,
 * `title`, `delay`.
 */
export function mockSchedule() {
    const rooms = mockRooms();

    const types = [
        { id: 1, name: "Panel", color: "#3b82f6" },
        { id: 2, name: "Workshop", color: "#10b981" },
        { id: 3, name: "Performance", color: "#f59e0b" },
        { id: 4, name: "Social", color: "#ec4899" },
    ];

    const organizers = [
        { id: 1, name: "Programming Team" },
        { id: 2, name: "Guest Crew" },
        { id: 3, name: "Volunteers" },
    ];

    const entries = [
        { title: "Opening Ceremony", room: 0, type: 0, org: 0, start: -90, len: 60, delay: 0 },
        { title: "Welcome to the Con", room: 1, type: 0, org: 0, start: -30, len: 45, delay: 0 },
        { title: "Beginner Fursuit Making", room: 2, type: 1, org: 2, start: -15, len: 90, delay: 10 },
        { title: "Live Music Showcase", room: 0, type: 2, org: 1, start: 30, len: 60, delay: 0 },
        { title: "Art Critique Roundtable", room: 3, type: 0, org: 0, start: 45, len: 50, delay: 0 },
        { title: "Dealers Den Meet & Greet", room: 4, type: 3, org: 2, start: 60, len: 120, delay: 25 },
        { title: "Digital Painting 101", room: 1, type: 1, org: 2, start: 90, len: 75, delay: 0 },
        { title: "Dance Competition", room: 0, type: 2, org: 1, start: 150, len: 90, delay: 0 },
        { title: "Charity Auction", room: 0, type: 3, org: 0, start: 240, len: 60, delay: 0 },
        { title: "Closing Ceremony", room: 0, type: 0, org: 0, start: 300, len: 60, delay: 0 },
    ];

    return entries.map((e, index) => ({
        id: index + 1,
        title: e.title,
        starts_at: iso(e.start),
        ends_at: iso(e.start + e.len),
        delay: e.delay,
        room: rooms[e.room],
        scheduleType: types[e.type],
        scheduleOrganizer: organizers[e.org],
    }));
}

export function mockAnnouncements() {
    return [
        {
            id: 1,
            title: "Welcome!",
            content: "Welcome to the convention. Check the schedule for today's events.",
            created_at: iso(-120),
            updated_at: iso(-120),
        },
        {
            id: 2,
            title: "Lost & Found",
            content: "Lost something? Visit the info desk near the main entrance.",
            created_at: iso(-60),
            updated_at: iso(-60),
        },
        {
            id: 3,
            title: "Photo Policy",
            content: "Please ask before taking photos of fursuiters. Be respectful.",
            created_at: iso(-30),
            updated_at: iso(-30),
        },
    ];
}

export function mockArtworks() {
    return [
        {
            id: 1,
            name: "Aurora Fields",
            artist: "Jamie Rivers",
            horizontal: "https://placehold.co/1920x1080/1e293b/ffffff?text=Aurora+Fields",
            vertical: "https://placehold.co/1080x1920/1e293b/ffffff?text=Aurora+Fields",
            banner: "https://placehold.co/1920x480/1e293b/ffffff?text=Aurora+Fields",
        },
        {
            id: 2,
            name: "Neon Den",
            artist: "Sky Vasquez",
            horizontal: "https://placehold.co/1920x1080/312e81/ffffff?text=Neon+Den",
            vertical: "https://placehold.co/1080x1920/312e81/ffffff?text=Neon+Den",
            banner: "https://placehold.co/1920x480/312e81/ffffff?text=Neon+Den",
        },
        {
            id: 3,
            name: "Quiet Forest",
            artist: "Robin Ash",
            horizontal: "https://placehold.co/1920x1080/064e3b/ffffff?text=Quiet+Forest",
            vertical: "https://placehold.co/1080x1920/064e3b/ffffff?text=Quiet+Forest",
            banner: "https://placehold.co/1920x480/064e3b/ffffff?text=Quiet+Forest",
        },
    ];
}

export const mockData = {
    screen: mockScreen,
    rooms: mockRooms,
    schedule: mockSchedule,
    announcements: mockAnnouncements,
    artworks: mockArtworks,
};

export default mockData;

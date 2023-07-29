<script setup>
defineProps(['schedule', 'announcements'])
import PageTitle from "@/Components/PageTitle.vue";
import Card from "@/Components/Card.vue";

let options = {
    hour: "2-digit", minute: "2-digit"
};

let scrollingDown = true;
pageScroll()

function pageScroll() {
    const windowHeight = window.innerHeight;
    const pageHeight = document.body.scrollHeight;

    // Check if we reached the bottom of the page
    if (window.scrollY + windowHeight >= pageHeight) {
        if (scrollingDown === true) {
            setTimeout(() => {
                scrollingDown = false;
            }, 5000);
        }
    } else if (window.scrollY <= 0) {
        // Reached the top of the page, start scrolling down again
        scrollingDown = true;
    }

    // Scroll down or up depending on the scrollingDown flag
    if (scrollingDown) {
        window.scrollBy(0, 1); // Scroll down by 1 pixel
    } else {
        window.scrollBy(0, -1); // Scroll up by 1 pixel
    }

    // Repeat the scrolling process after a short delay (10ms in this case)
    setTimeout(pageScroll, 50);
}

</script>

<template>
    <div class="gap-8 overflow-y-auto"
         :class="{'flex flex-col f xl:flex-row': announcements.length > 0 && schedule.length > 0}">
        <div :class="{'xl:w-1/2': announcements.length > 0}" v-if="schedule.length > 0">
            <Card class="p-6 space-y-8">
                <div class="text-white flex justify-between flex-row" v-for="entry in schedule">
                    <div>
                        <div class="text-4xl themeFont">{{ entry.title }}</div>
                        <div class="text-3xl themeFont text-secondary">{{ entry.room }}</div>
                    </div>
                    <div class="flex gap-3">
                        <div>
                            <div
                                class="inline-block bg-red-500 text-white font-bold shadow-2xl rounded-full py-2 px-4 text-sm">
                                ONGOING <span class="animate-blink">●</span>
                            </div>
                        </div>
                        <div class="text-2xl themeFont">
                            {{ new Date(entry.starts_at).toLocaleTimeString('de-DE', options) }}
                            - {{ new Date(entry.ends_at).toLocaleTimeString('de-DE', options) }}
                        </div>
                    </div>
                </div>
            </Card>
        </div>
        <div :class="{'xl:w-1/2': schedule.length > 0}" class="space-y-6" v-if="announcements.length > 0">
            <Card class="p-6 bg-orange-700" v-for="entry in announcements">
                <div class="text-white flex justify-between flex-row-reverse">
                    <div class="text-2xl themeFont">{{ new Date(entry.starts_at).toLocaleTimeString('de-DE', options) }}
                    </div>
                    <div>
                        <div class="text-4xl themeFont">{{ entry.title }}</div>
                        <p class="text-3xl whitespace-pre-wrap">{{ entry.content }}</p>
                    </div>
                </div>
            </Card>
        </div>
        <div class="p-6 w-full h-full mt-32 flex justify-center items-center text-center"
             v-if="schedule.length < 1 && announcements.length < 1">
            <div class="themeFont text-white text-7xl">There are currently no entries</div>
        </div>
    </div>
</template>

<style scoped>
/* Styles for the chip */
.chip {
    display: inline-block;
    background-color: #e0e0e0;
    color: #333;
    border-radius: 16px;
    padding: 8px 12px;
    font-size: 14px;
    cursor: pointer;
}

/* Styles for the animated blinking effect */
@keyframes blink {
    0% {
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
    100% {
        opacity: 0;
    }
}

.animate-blink {
    animation: blink 1s infinite;
}
</style>

<template>
    <div class="video-player-container">
        <div class="video-player" :class="{ 'is-playing': isPlaying }">
            <div class="video-player__controls" :style="{ backgroundColor: controlsBackgroundColor }" @click="onControlsClick">
                <Transition name="fade-in">
                    <button v-if="isPlaying" class="video-player__pause-button icon-pause" type="button"></button>
                    <button v-else class="video-player__play-button icon-play-video" type="button"></button>
                </Transition>
                <div class="video-player__track" ref="track">
                    <div class="video-player__track-upper">
                        <div class="video-player__timer">
                            <span class="video-player__timer-current">
                                {{ currentTime }}
                            </span>
                            <span class="video-player__timer-delimeter">/</span>
                            <span class="video-player__timer-duration">
                                {{ durationTime }}
                            </span>
                        </div>
                        <button class="video-player__expand icon-expand" @click="expandOrShrink"></button>
                    </div>
                    <div class="video-player__scale" ref="scale" @pointerdown="onScalePointerdown">
                        <div class="video-player__bar" ref="bar">
                            <div class="video-player__toddler" ref="toddler" @pointerdown="onToddlerPointerdown"></div>
                        </div>
                    </div>
                </div>
            </div>
            <video class="video-player__video" :src="videoSrc" muted loop preload="metadata" ref="video"
                @loadedmetadata="onVideoLoad" @timeupdate="onTimeupdate" @click="pauseOrPlay"></video>
        </div>
    </div>
</template>

<script>
import { formatSeconds, getCoords } from '@/assets/scripts/scripts.js';
import { useModalsStore } from '@/stores/modals.js';

export default {
    name: 'VideoPlayer',
    props: {
        src: {
            type: String,
            required: true
        }
    },
    mounted() {
        this.$refs.scale.ondragstart = () => false;
        this.$refs.bar.ondragstart = () => false;
        this.$refs.toddler.ondragstart = () => false;

        this.intersectionObs = new IntersectionObserver((entries) => {
            for  (let entry of entries) {
                if  (entry.target === this.$el) {
                    if  (entry.isIntersecting) {
                        this.videoSrc = this.src;
                        this.intersectionObs.disconnect();
                    }
                    break;
                }
            }
        }, {
            root: null,
            threshold: 0.2
        });
        this.intersectionObs.observe(this.$el);
    },
    data() {
        return {
            intersectionObs: null,
            // меняется благодаря IntersectionObserver
            videoSrc: '',
            isPlaying: false,
            isVideoInitted: false,
            durationTime: '00:00',
            currentTime: '00:00',
            currentTimeSeconds: 0,
            durationTimeSeconds: 0,
            isExpanded: false,
        }
    },
    methods: {
        onVideoLoad() {
            this.onTimeupdate();
        },
        onControlsClick(event) {
            const playPauseExceptions = ['.video-player__track'];
            let isPlayPauseException = false;
            for (let selector of playPauseExceptions) {
                if (event.target.closest(selector)) {
                    isPlayPauseException = true;
                    break;
                }
            }
            if (isPlayPauseException == false)
                this.pauseOrPlay();
        },

        // пауза/воспроизведение
        pauseOrPlay() {
            this.isPlaying = !this.isPlaying;
            this.isPlaying ? this.play() : this.pause();
        },
        play() {
            this.isPlaying = true;
            this.$refs.video.play();
        },
        pause() {
            this.isPlaying = false;
            this.$refs.video.pause();
        },
        // конец - пауза/воспроизведение

        // перемотка, управление шкалой воспроизведения
        onTimeupdate() {
            if (!this.$refs)
                return;
            if (!this.$refs.video)
                return;

            const durationTimeSeconds = parseInt(this.$refs.video.duration);
            const durationTime = formatSeconds(durationTimeSeconds);
            if (this.durationTime !== durationTime)
                this.durationTime = durationTime;
            if (this.durationTimeSeconds !== durationTimeSeconds)
                this.durationTimeSeconds = durationTimeSeconds;

            if (!this.isMovingToddlerByPointer) {
                const currentTimeSeconds = this.$refs.video.currentTime;
                this.currentTimeSeconds = currentTimeSeconds;
                this.currentTime = formatSeconds(currentTimeSeconds);
            }

            this.moveToddlerByTime();
        },
        moveToddlerByTime() {
            // когда пользователь двигает ползунок, данный метод не отрабатывает
            if (this.isMovingToddlerByPointer)
                return;

            let percent = parseInt(this.currentTimeSeconds / (this.durationTimeSeconds / 100));
            this.setBarWidthByPercent(percent);
        },
        setBarWidthByPercent(percent) {
            if (percent < 0)
                percent = 0;
            if (percent > 100)
                percent = 100;

            this.$refs.bar.style.width = `${percent}%`;
        },
        onToddlerPointerdown() {
            this.isMovingToddlerByPointer = true;
            const self = this;
            const scaleCoords = getCoords(this.$refs.scale);
            const scaleWidth = scaleCoords.right - scaleCoords.left;
            const toddlerOffset = self.$refs.toddler.offsetWidth / 2;

            let percent = parseInt(this.$refs.bar.style.width.replace(/[^.0-9]/));
            percent = percent + toddlerOffset / (scaleWidth / 100);

            document.addEventListener('pointermove', onPointermove);
            document.addEventListener('pointerup', onPointerup);

            function onPointermove(moveEvent) {
                const pageX = moveEvent.pageX || moveEvent.detail.pageX;
                const toddlerX = pageX - scaleCoords.left + toddlerOffset;
                percent = parseInt(toddlerX / (scaleWidth / 100));
                self.setBarWidthByPercent(percent);

                calcCurrentTime(percent);
            }
            function onPointerup() {
                // изменить currentTime
                percent = parseInt(percent);
                if (percent || percent === 0) {
                    calcCurrentTime(percent);
                    self.$refs.video.currentTime = self.currentTimeSeconds;
                }
                // убрать обработчик
                document.removeEventListener('pointermove', onPointermove);
                document.removeEventListener('pointerup', onPointerup);
                self.isMovingToddlerByPointer = false;
            }
            function calcCurrentTime(percent) {
                self.currentTimeSeconds = self.durationTimeSeconds / 100 * percent;
                self.currentTime = formatSeconds(self.currentTimeSeconds);
            }
        },
        onScalePointerdown(event) {
            const scaleCoords = getCoords(this.$refs.scale);
            const scaleWidth = scaleCoords.right - scaleCoords.left;
            const clickX = event.pageX - scaleCoords.left;
            const percent = clickX / (scaleWidth / 100);
            this.setBarWidthByPercent(percent);

            this.onToddlerPointerdown();
            document.dispatchEvent(new CustomEvent('pointermove', { detail: { pageX: event.pageX } }));
        },
        // конец - перемотка, управление шкалой воспроизведения

        // на весь экран
        expandOrShrink() {
            this.isExpanded = !this.isExpanded;
            const store = useModalsStore();
            store.exportForMediaModal(this);
        }
        // конец - на весь экран
    },
    computed: {
        controlsBackgroundColor(){
            if(this.isPlaying) 
                return 'transparent';

            return 'rgba(0, 0, 0, 0.25)';
        }
    }
}
</script>

<style>
.video-player__pause-button.fade-in-enter-active,
.video-player__pause-button.fade-in-leave-active {
    position: absolute;
}
</style>
<script setup>

import {computed, onMounted, onUnmounted, ref} from "vue";
(function () { //polyfill
    let lastTime = 0;
    let vendors = ['ms', 'moz', 'webkit', 'o'];
    for (var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
        window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
        window.cancelAnimationFrame = window[vendors[x] + 'CancelAnimationFrame']
            || window[vendors[x] + 'CancelRequestAnimationFrame'];
    }

    if (!window.requestAnimationFrame)
        window.requestAnimationFrame = function (callback, element) {
            let currTime = new Date().getTime();
            let timeToCall = Math.max(0, 16 - (currTime - lastTime));
            let id = window.setTimeout(function () {
                    callback(currTime + timeToCall);
                },
                timeToCall);
            lastTime = currTime + timeToCall;
            return id;
        };

    if (!window.cancelAnimationFrame)
        window.cancelAnimationFrame = function (id) {
            clearTimeout(id);
        };
}());

const props = defineProps({
    color: {
        type: String,
        default: "purple"
    }
})
//color: want to overload default layout color as like a tint to "recolor" to other "Classical elements" / Elemental (green/yellow/red/blue)
// refer to e.g. earth, water, air, fire to represent e.g. categories like Service, Dances, Panels each in the color of a diffrent Elemental.


let spritePurple = new Image();
import spritePurpleSrc from "@/Projects/EF27/Assets/images/smoke_purple3.png";
spritePurple.src = spritePurpleSrc;

let spriteGreen = new Image();
import spriteGreenSrc from "@/Projects/EF27/Assets/images/smoke_green3.png";
spriteGreen.src = spriteGreenSrc;

let spriteYellow = new Image();
import spriteYellowSrc from "@/Projects/EF27/Assets/images/smoke_yellow3.png";
spriteYellow.src = spriteYellowSrc;

let spriteBlue = new Image();
import spriteBlueSrc from "@/Projects/EF27/Assets/images/smoke_blue3.png";
spriteBlue.src = spriteBlueSrc;

let spriteRed = new Image();
import spriteRedSrc from "@/Projects/EF27/Assets/images/smoke_red3.png";
spriteRed.src = spriteRedSrc;

const sprites = {
    purple: spritePurple,
    green: spriteGreen,
    yellow: spriteYellow,
    blue: spriteBlue,
    red: spriteRed
};

// const fpsView = ref();
const mistLayer = ref();

function generateRandom(min, max) {
    return Math.random() * (max - min) + min;
}

function MagicMist(uid, canvas, options = {}) {

    this.idx = uid;
    this.intervals = [];
    this.particles = [];

    this.particleCount = (options.particleCount && !isNaN(options.particleCount)) ? options.particleCount : generateRandom(0, 10);
    this.maxxVelocity = (options.maxxVelocity && !isNaN(options.maxxVelocity)) ? options.maxxVelocity : generateRandom(0, 1);
    this.maxyVelocity = (options.maxyVelocity && !isNaN(options.maxyVelocity)) ? options.maxyVelocity : generateRandom(0, 1);
    this.disturbance = 0;

    this.canvas = canvas;

    this.context;
    this.bufferCanvas = document.createElement('canvas');
    this.bufferContext = this.bufferCanvas.getContext("2d");

    this.bufferCanvas.width = this.canvas.width = (options.width && !isNaN(options.width)) ? options.width : window.innerWidth;
    this.bufferCanvas.height = this.canvas.height = (options.height && !isNaN(options.height)) ? options.height : window.innerHeight;

    if (this.canvas.getContext) {

        this.context = this.canvas.getContext('2d');

        try {
            let storedParticles = JSON.parse(localStorage.getItem('layer_' + this.idx + '_particles'));

            let velocity = {maxxVelocity: this.maxxVelocity, maxyVelocity: this.maxyVelocity};
            if (options.particleOptions) {
                Object.assign(options.particleOptions, velocity, options.particleOptions);
            }

            for (let i = 0; i < storedParticles.length; i++) {
                if (options.particleOptions) {
                    Object.assign(storedParticles[i], options.particleOptions);
                }
                this.particles.push(new Particle(i, this.particles, this.bufferCanvas, this.bufferContext, storedParticles[i]));
            }

            if (this.particles.length < this.particleCount) {
                for (let i = this.particles.length; i < this.particleCount; i++) {
                    this.particles.push(new Particle(i, this.particles, this.bufferCanvas, this.bufferContext, options.particleOptions));
                }

            } else if (this.particles.length > this.particleCount) {
                for (let i = this.particles.length - 1; i > this.particleCount - 1; i--) {
                    this.particles[i].kill();
                }
            }

            localStorage.setItem('layer_' + this.idx + '_particles', JSON.stringify(this.particles));

        } catch (e) {
            console.error(e);
            localStorage.removeItem('layer_' + this.idx + '_particles');
            for (var i = 0; i < this.particleCount; ++i) {
                this.particles.push(new Particle(i, this.particles, this.bufferCanvas, this.bufferContext, options.particleOptions));
            }
            localStorage.setItem('layer_' + this.idx + '_particles', JSON.stringify(this.particles));
        }

    }

// Update the scene
    this.rander = function (elapsedTime) {
        this.bufferContext.clearRect(0, 0, this.bufferCanvas.width, this.bufferCanvas.height);
        let that = this;
        this.particles.forEach(function (particle) {
            // particle.clear();
            particle.setDisturbance(that.disturbance);
            particle.update(elapsedTime);
            particle.draw();
        });
        localStorage.setItem('layer_' + this.idx + '_particles', JSON.stringify(this.particles));
        this.context.clearRect(0, 0, this.bufferCanvas.width, this.bufferCanvas.height);
        this.context.drawImage(this.bufferCanvas, 0, 0);
        return this;
    };

    this.onResize = function (width, height) {
        this.bufferCanvas.width = this.canvas.width = (options.width && !isNaN(options.width)) ? options.width : (width && !isNaN(width)) ? width : window.innerWidth;
        this.bufferCanvas.height = this.canvas.height = (options.height && !isNaN(options.height)) ? options.height : (height && !isNaN(height)) ? height : window.innerHeight;
    }

    this.start = function () {
        this.canvas.style.visibility = "visible";
        let that = this;
        this.intervals.push(setInterval(function () {
            that.disturbance = 0.05 * (that.disturbance - generateRandom(-that.maxxVelocity, that.maxyVelocity));
        }, 10000));
    }

    this.stop = function () {
        this.canvas.style.visibility = "hidden";
        this.intervals.forEach(function (interval) {
            clearInterval(interval);
        });
        return this;
    };

    this.shutdown = function () {
        this.particles.forEach(function (particle) {
            particle.kill();
        });
        return this;
    };

    function Particle(idx, particles, bufferCanvas, bufferContext, options = {}) {

        this.idx = idx;
        this.killed = (options.killed) ? options.killed : false;

        // this.particles = particles;
        this.bufferCanvas = bufferCanvas;
        this.bufferContext = bufferContext;

        this.radius = generateRandom(25, 100);

        this.sprite = {uid: "green"};
        if (options.sprite) {
            Object.assign(this.sprite, options.sprite);
        }
        this.image = {height: this.radius, width: this.radius};
        let img = sprites[this.sprite.uid];
        if (img) {
            this.image = img;
        }

        this.possition = {x: 0, y: 0};
        this.disturbance = 0;
        this.maxxVelocity = (options.maxxVelocity && !isNaN(options.maxxVelocity)) ? options.maxxVelocity : generateRandom(0, 1);
        this.maxyVelocity = (options.maxyVelocity && !isNaN(options.maxyVelocity)) ? options.maxyVelocity : generateRandom(0, 1);
        this.offset = (options.offset && !isNaN(options.offset)) ? options.offset : generateRandom(-2 * (this.scale * this.image.width), 2 * (this.scale * this.image.width));
        this.frequency = (options.frequency && !isNaN(options.frequency)) ? options.frequency : generateRandom(0, this.bufferCanvas.width / (this.scale * this.image.width) / 2);
        this.angle = (options.angle && !isNaN(options.angle)) ? options.angle : generateRandom(0, 2 * Math.PI);
        this.opacity = (options.opacity && !isNaN(options.opacity)) ? options.opacity : 0;
        this.scale = (options.scale && !isNaN(options.scale)) ? options.scale : 0;

        this.isFadeIn = (options.isFadeIn) ? options.isFadeIn : true;
        this.target = { //{x: 0, y: 0, opacity: 1, scale: 1}
            x: generateRandom(-(this.scale * this.image.width) / 2, this.bufferCanvas.width + (this.scale * this.image.height) / 2),
            y: generateRandom(-(this.scale * this.image.height) / 2, this.bufferCanvas.height + (this.scale * this.image.height)),
            opacity: generateRandom(0.5, 1),
            scale: generateRandom(0.5, 1.25)
        };

        if (options.target) {
            Object.assign(this.target, options.target);
        }

        this.possition.x = (options.possition && options.possition.x && !isNaN(options.possition.x)) ? options.possition.x : generateRandom(0, this.bufferCanvas.width);
        this.possition.y = (options.possition && options.possition.y && !isNaN(options.possition.y)) ? options.possition.y : this.bufferCanvas.height + (this.scale * this.image.height) / 2;
        this.xInitialVelocity = (options.xInitialVelocity && !isNaN(options.xInitialVelocity)) ? options.xInitialVelocity : generateRandom(0, this.maxxVelocity);
        this.yInitialVelocity = (options.yInitialVelocity && !isNaN(options.yInitialVelocity)) ? options.yInitialVelocity : generateRandom(this.maxyVelocity / 2, this.maxyVelocity);
        this.xCurrentVelocity = (options.xCurrentVelocity && !isNaN(options.xCurrentVelocity)) ? options.xCurrentVelocity : this.xInitialVelocity;
        this.yCurrentVelocity = (options.yCurrentVelocity && !isNaN(options.yCurrentVelocity)) ? options.yCurrentVelocity : this.yInitialVelocity;

        this.reset = function () {

            if (this.killed) {
                particles.splice(this.idx, 1);
                return;
            }
            this.opacity = 0;
            this.scale = generateRandom(0.5, 1.5);
            this.offset = generateRandom(-2 * (this.scale * this.image.width), 2 * (this.scale * this.image.width));

            this.frequency = generateRandom(0, this.bufferCanvas.width / (this.scale * this.image.width) / 2);

            this.target.x = generateRandom(-(this.scale * this.image.width) / 2, this.bufferCanvas.width + (this.scale * this.image.height) / 2);
            this.target.y = generateRandom(-(this.scale * this.image.height) / 2, this.bufferCanvas.height + (this.scale * this.image.height));
            this.target.opacity = generateRandom(0.5, 1);
            this.target.scale = generateRandom(0.5, 1.25);

            this.angle = generateRandom(0, 2 * Math.PI);


            if (Math.random() > 0.5) {
                this.possition.x = -(this.scale * this.image.width) / 2;
                this.possition.y = generateRandom(-(this.scale * this.image.height) / 2, this.bufferCanvas.height + (this.scale * this.image.height));
            } else {
                this.possition.x = generateRandom(-(this.scale * this.image.width) / 2, this.bufferCanvas.width + (this.scale * this.image.height) / 2);
                this.possition.y = this.bufferCanvas.height + (this.scale * this.image.height) / 2;
            }
            this.xCurrentVelocity = this.xInitialVelocity = generateRandom(0, this.maxxVelocity);
            this.yCurrentVelocity = this.yInitialVelocity = generateRandom(this.maxyVelocity / 2, this.maxyVelocity);

            this.isFadeIn = true;
            return this;
        }

        function drawImage(ctx, img, x, y, angle = 0, scale = 1) {
            ctx.save();
            ctx.translate(x + img.width * scale / 2, y + img.height * scale / 2);
            ctx.rotate(angle);
            ctx.translate(-x - img.width * scale / 2, -y - img.height * scale / 2);
            ctx.drawImage(img, x, y, img.width * scale, img.height * scale);
            ctx.restore();
        }

// The function to draw the particle on the canvas.
        this.draw = function () {

            this.bufferContext.globalAlpha = this.opacity;

            if (this.image.src) {
                drawImage(this.bufferContext, this.image, this.possition.x - (this.scale * this.image.width) / 2, this.possition.y - (this.scale * this.image.height) / 2, this.angle, this.scale);
                // this.bufferContext.drawImage(this.image, this.x - (this.scale*this.image.width) / 2, this.y - (this.scale*this.image.height) / 2);
            } else {
                this.bufferContext.beginPath();
                this.bufferContext.arc(this.possition.x, this.possition.y, this.radius, 0, 2 * Math.PI, false);
                // let gradient = this.bufferContext.createRadialGradient(this.possition.x, this.possition.y, 0, this.possition.x, this.possition.y, this.radius);
                // gradient.addColorStop(0.75, "rgba(80, 20, 200, 0)");
                // gradient.addColorStop(0.95, "rgba(80, 20, 200, 1)");
                // this.bufferContext.fillStyle = gradient;
                this.bufferContext.fillStyle = "rgba(200, 20, 200, 1)";
                this.bufferContext.fill();
                this.bufferContext.closePath();
            }
            return this;
        };

// The function to draw the particle on the canvas.
        this.clear = function () {
            // this.bufferContext.beginPath();
            this.bufferContext.clearRect(this.possition.x, this.possition.y, (this.scale * this.image.width), (this.scale * this.image.height));
            // this.bufferContext.fillStyle = "black";
            // this.bufferContext.fill();
            // this.bufferContext.closePath();
            return this;
        };

// Update the particle.
        this.update = function () {
            // Update the position of the particle with the addition of the velocity.

            this.yCurrentVelocity = this.yInitialVelocity + this.disturbance;
            this.xCurrentVelocity = this.xInitialVelocity + this.disturbance;

            let steps = ((this.bufferCanvas.width) / this.xCurrentVelocity);
            this.angle += this.frequency * this.xCurrentVelocity / (180 * Math.PI);

            if (this.isFadeIn) {

                let fadeInX = true;
                let fadeInY = true;
                let fadeInO = true;
                let fadeInS = true;

                let diffX = (this.target.x - this.possition.x);
                if (Math.abs(diffX) > this.xInitialVelocity) {
                    if (this.target.x > this.possition.x) {
                        this.possition.x += (this.xCurrentVelocity);
                    } else {
                        this.possition.x -= (this.xCurrentVelocity);
                    }
                } else {
                    fadeInX = false;
                }

                let diffY = (this.target.y - this.possition.y);
                if (Math.abs(diffY) > this.yInitialVelocity) {
                    if (this.target.y > this.possition.y) {
                        this.possition.y += (this.yCurrentVelocity);
                    } else {
                        this.possition.y -= (this.yCurrentVelocity);
                    }
                } else {
                    fadeInY = false;
                }

                let diffO = (this.target.opacity - this.opacity);
                if (Math.abs(diffO) > 0.001) {
                    if (this.target.opacity > this.opacity) {
                        this.opacity += 0.001;
                    } else {
                        this.opacity -= 0.001;
                    }
                } else {
                    fadeInO = false;
                }

                let diffS = (this.target.scale - this.scale);
                if (Math.abs(diffS) > 0.001) {
                    if (this.target.scale > this.scale) {
                        this.scale += 0.001;
                    } else {
                        this.scale -= 0.001;
                    }
                } else {
                    fadeInS = false;
                }

                this.isFadeIn = fadeInX || fadeInY || fadeInO || fadeInS;

            } else {

                //fadeout
                if (this.opacity > 0.001) {
                    this.opacity -= 1 / steps;
                } else {
                    this.opacity = 0;
                }

                this.yCurrentVelocity = this.yInitialVelocity + this.disturbance;
                this.possition.y += this.yCurrentVelocity;

                this.xCurrentVelocity = this.xInitialVelocity + this.disturbance;
                this.possition.x += this.xCurrentVelocity;

            }


            // Check if has crossed the right edge
            if (this.possition.x > this.bufferCanvas.width + (this.scale * this.image.width) / 2) {
                this.reset();
            }
            // Check if has crossed the left edge
            else if (this.possition.x < -(this.scale * this.image.width) / 2) {
                this.reset();
            }
            // Check if has crossed the top edge
            if (this.possition.y + (this.scale * this.image.height) < 0) {
                this.reset();
            }
            if (this.opacity <= 0) {
                this.reset();
            }


            return this;
        };

        this.kill = function () {
            this.killed = true;
            return this;
        }

        this.setDisturbance = function (disturbance) {
            this.disturbance = disturbance;
            return this;
        }

    }
}

function onResize() {
    magicMistLayerController.onResize(window.innerWidth, window.innerHeight);
}

function MagicMistLayerController(fps = 30) {

    this.magicMistLayers = [];
    this.animationRequest;

    this.start = function () {
        this.magicMistLayers.forEach(function (magicMist) {
            magicMist.start();
        });

        let fpsInterval, controlTime, now, then, elapsed;
        let frameCount = 0;

        let that = this;

        function animationLoop() {
            that.animationRequest = window.requestAnimationFrame(animationLoop);

            now = Date.now();
            elapsed = now - then;
            if (elapsed > fpsInterval) {

                // Get ready for next frame by setting then=now, but...
                // Also, adjust for fpsInterval not being multiple of 16.67
                then = now - (elapsed % fpsInterval);

                // draw stuff here
                that.magicMistLayers.forEach(function (magicMist) {
                    magicMist.rander(elapsed);
                });

                // TESTING...Report #seconds since start and achieved fps.
                var sinceStart = now - controlTime;
                var currentFps = Math.round(1000 / (sinceStart / ++frameCount) * 100) / 100;
                // fpsView.value.textContent = (/*"Elapsed time= " + Math.round(sinceStart / 1000 * 100) / 100 + " secs @ " + */ ("" + currentFps.toFixed(2)).padStart(6, '0') + " fps.");

                if (sinceStart > 60000) {
                    controlTime = now;
                    frameCount = 0;
                }

            }

        };

        fpsInterval = 1000 / fps;
        then = Date.now();
        controlTime = then;
        animationLoop();

        return this;
    };

    this.stop = function () {
        this.magicMistLayers.forEach(function (magicMist) {
            magicMist.stop();
        });
        window.cancelAnimationFrame(this.animationRequest);
        return this;
    };

    this.shutdown = function () {
        this.magicMistLayers.forEach(function (magicMist) {
            magicMist.shutdown();
        });
        this.stop();
        return this;
    };

    this.onResize = function (width, height) {
        this.magicMistLayers.forEach(function (magicMist) {
            magicMist.onResize(width, height);
        });
        return this;
    }

    this.add = function (magicMist) {
        this.magicMistLayers.push(magicMist);
        return this;
    }
}

const magicMistLayerController = new MagicMistLayerController();


onMounted(() => {

    window.addEventListener('resize', onResize);

    magicMistLayerController
        .add(new MagicMist(0, mistLayer.value, {
            particleCount: 40,
            maxxVelocity: 0.5,
            maxyVelocity: 0.5,
            particleOptions: {
                sprite: {uid: props.color},
            }
        }))
        .start();
})

onUnmounted(() => {
    window.removeEventListener('resize', onResize);
    magicMistLayerController.stop();
});

</script>


<template>

    <canvas ref="mistLayer" id="MagicMistLayer1" style="z-index: 4; visibility: hidden"
            class="absolute left-0 top-0 w-screen h-screen"></canvas>

<!--    <div ref="fpsView" class="absolute bg-black z-50  top-0 right-0 text-2xl font-bold text-white rounded-br"></div>-->


</template>


<style scoped>
</style>


<style>
</style>


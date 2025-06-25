<template>
    <div class="story-container">
        <!-- Background Music (Optional) -->
        <audio ref="backgroundMusic" loop>
            <source
                :src="baseImageUrl + '/gentle-background.mp3'"
                type="audio/mpeg"
            />
        </audio>

        <!-- Navigation -->
        <div class="fixed top-4 left-4 z-50">
            <router-link
                to="/"
                class="text-gray-600 hover:text-gray-800 transition-colors"
            >
                <svg
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"
                    ></path>
                </svg>
            </router-link>
        </div>

        <!-- Music Control -->
        <div class="fixed top-4 right-4 z-50">
            <button
                @click="toggleMusic"
                class="text-gray-600 hover:text-gray-800 transition-colors"
            >
                <svg
                    v-if="isMusicPlaying"
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"
                    ></path>
                </svg>
                <svg
                    v-else
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    ></path>
                </svg>
            </button>
        </div>

        <!-- Story Sections -->
        <div class="story-sections">
            <!-- Section 1: Title -->
            <section class="story-section" ref="titleSection">
                <div
                    class="story-content"
                    :class="{ 'animate-in': titleVisible }"
                >
                    <h1 class="story-title">
                        Gửi đến cậu (Cheese Vu) – người từng đi cùng tớ qua một
                        quãng đường đẹp nhất…
                    </h1>
                </div>
            </section>

            <!-- Section 2: Story -->
            <section class="story-section" ref="storySection">
                <div
                    class="story-content"
                    :class="{ 'animate-in': storyVisible }"
                >
                    <div class="story-text">
                        <p class="story-paragraph">
                            Mình đã cùng nhau bước qua hơn 5 năm – đầy thử
                            thách, tổn thương nhưng cũng ngập tràn yêu thương và
                            hy vọng.
                        </p>
                        <p class="story-paragraph">
                            Cậu từng nói cậu muốn làm công việc mà cậu thực sự
                            yêu thích. Và tớ luôn nhớ điều đó. Chính vì vậy, đây
                            là món quà tớ đã âm thầm bắt đầu từ khi chúng mình
                            chia tay – như một phần trong giấc mơ của cậu.
                        </p>
                        <p class="story-paragraph">
                            Tớ từng nghĩ sẽ chính tay gửi món quà này cho cậu,
                            như một điều cuối cùng tớ có thể làm cho người mà tớ
                            từng yêu bằng cả trái tim.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Section 3: Reason -->
            <section class="story-section" ref="reasonSection">
                <div
                    class="story-content"
                    :class="{ 'animate-in': reasonVisible }"
                >
                    <div class="story-text">
                        <h2 class="story-subtitle">01/05/2025</h2>
                        <p class="story-paragraph">
                            Trang web này không chỉ là vài dòng chữ, mà là lời
                            hứa còn dang dở. Là nỗi nhớ, là những đêm dài không
                            ngủ, là điều cuối cùng tớ có thể dành cho cậu.
                        </p>
                        <p class="story-paragraph">
                            Có thể bây giờ tớ không còn là một phần trong cuộc
                            sống của cậu nữa – vì cậu đã có người mới để yêu
                            thương và sẻ chia. Nhưng tớ vẫn mong cậu sẽ luôn
                            mạnh mẽ và tìm được hạnh phúc mà cậu xứng đáng.
                        </p>
                        <p class="story-paragraph">
                            Tớ đã từng cố giữ liên lạc, thậm chí chấp nhận làm
                            bạn… chỉ để được tiếp tục nói chuyện với cậu. Nhưng
                            có lẽ đã đến lúc tớ học cách buông tay. Vì người tớ
                            yêu thương… không còn dành cho tớ nữa.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Section 4: Final Button -->
            <section class="story-section" ref="finalSection">
                <div
                    class="story-content"
                    :class="{ 'animate-in': finalVisible }"
                >
                    <div class="final-content">
                        <button
                            v-if="!showFinalMessage"
                            @click="showFinalMessage = true"
                            class="final-button"
                        >
                            Cảm ơn vì đã từng cùng nhau.
                        </button>
                        <div v-else class="final-message">
                            Đây là món quà cuối cùng... Và cũng là lời tạm biệt
                            cuối cùng. Cảm ơn vì đã từng cùng nhau. Tạm biệt
                            nhé, người đã từng là tất cả.
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Floating Elements -->
        <div class="floating-elements">
            <div
                class="floating-element"
                v-for="i in 6"
                :key="i"
                :style="getFloatingStyle(i)"
            ></div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const backgroundMusic = ref(null);
const isMusicPlaying = ref(false);
const showFinalMessage = ref(false);
const baseImageUrl = import.meta.env.VITE_RESPONE_IMAGE_URL;

// Animation states
const titleVisible = ref(false);
const storyVisible = ref(false);
const reasonVisible = ref(false);
const finalVisible = ref(false);

// Section refs
const titleSection = ref(null);
const storySection = ref(null);
const reasonSection = ref(null);
const finalSection = ref(null);

const toggleMusic = async () => {
    if (backgroundMusic.value) {
        try {
            if (isMusicPlaying.value) {
                await backgroundMusic.value.pause();
                isMusicPlaying.value = false;
            } else {
                await backgroundMusic.value.play();
                isMusicPlaying.value = true;
            }
        } catch (error) {
            console.log("Music playback error:", error);
            // Don't change state on error
        }
    }
};

const getFloatingStyle = (index) => {
    const delay = index * 0.5;
    const duration = 3 + index * 0.5;
    return {
        animationDelay: `${delay}s`,
        animationDuration: `${duration}s`,
        left: `${10 + index * 15}%`,
        top: `${20 + index * 10}%`,
    };
};

const handleScroll = () => {
    const scrollY = window.scrollY;
    const windowHeight = window.innerHeight;

    // Title animation
    if (titleSection.value) {
        const titleRect = titleSection.value.getBoundingClientRect();
        if (titleRect.top < windowHeight * 0.8) {
            titleVisible.value = true;
        }
    }

    // Story animation
    if (storySection.value) {
        const storyRect = storySection.value.getBoundingClientRect();
        if (storyRect.top < windowHeight * 0.8) {
            storyVisible.value = true;
        }
    }

    // Reason animation
    if (reasonSection.value) {
        const reasonRect = reasonSection.value.getBoundingClientRect();
        if (reasonRect.top < windowHeight * 0.8) {
            reasonVisible.value = true;
        }
    }

    // Final animation
    if (finalSection.value) {
        const finalRect = finalSection.value.getBoundingClientRect();
        if (finalRect.top < windowHeight * 0.8) {
            finalVisible.value = true;
        }
    }
};

onMounted(() => {
    window.addEventListener("scroll", handleScroll);
    handleScroll(); // Check initial state

    // Set up music
    if (backgroundMusic.value) {
        backgroundMusic.value.volume = 0.3; // Set volume to 30%

        // Listen for music events to sync state
        backgroundMusic.value.addEventListener("play", () => {
            isMusicPlaying.value = true;
        });

        backgroundMusic.value.addEventListener("pause", () => {
            isMusicPlaying.value = false;
        });

        backgroundMusic.value.addEventListener("ended", () => {
            isMusicPlaying.value = false;
        });
    }
});

onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll);
    // Pause music when leaving the page
    if (backgroundMusic.value && isMusicPlaying.value) {
        backgroundMusic.value.pause();
    }
});
</script>

<style scoped>
.story-container {
    min-height: 100vh;
    background: linear-gradient(
        135deg,
        #fdf2f8 0%,
        #fce7f3 25%,
        #fbcfe8 50%,
        #f9a8d4 75%,
        #ec4899 100%
    );
    position: relative;
    overflow-x: hidden;
    font-family: "Times New Roman", "Inter", "Roboto", "Segoe UI", "Arial",
        serif;
}

.story-sections {
    position: relative;
    z-index: 10;
}

.story-section {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    position: relative;
}

.story-content {
    max-width: 800px;
    text-align: center;
    opacity: 0;
    transform: translateY(50px);
    transition: all 1s ease-out;
}

.story-content.animate-in {
    opacity: 1;
    transform: translateY(0);
}

.story-title {
    font-size: 2.5rem;
    font-weight: 300;
    color: #4a5568;
    line-height: 1.4;
    margin-bottom: 2rem;
    font-family: "Times New Roman", "Inter", "Roboto", "Segoe UI", "Arial",
        serif;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.story-text {
    font-size: 1.2rem;
    line-height: 1.8;
    color: #2d3748;
    font-family: "Times New Roman", "Inter", "Roboto", "Segoe UI", "Arial",
        serif;
}

.story-subtitle {
    font-size: 1.8rem;
    font-weight: 400;
    color: #4a5568;
    margin-bottom: 2rem;
    font-family: "Times New Roman", "Inter", "Roboto", "Segoe UI", "Arial",
        serif;
}

.story-paragraph {
    margin-bottom: 1.5rem;
    opacity: 0.9;
}

.final-button {
    background: linear-gradient(135deg, #ec4899, #f472b6);
    color: white;
    border: none;
    padding: 1rem 2rem;
    font-size: 1.1rem;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(236, 72, 153, 0.3);
    font-family: "Times New Roman", "Inter", "Roboto", "Segoe UI", "Arial",
        serif;
}

.final-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(236, 72, 153, 0.4);
}

.final-message {
    font-size: 1.5rem;
    color: #4a5568;
    font-family: "Times New Roman", "Inter", "Roboto", "Segoe UI", "Arial",
        serif;
    animation: fadeInUp 1s ease-out;
}

.floating-elements {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

.floating-element {
    position: absolute;
    width: 20px;
    height: 20px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    animation: float 4s ease-in-out infinite;
}

@keyframes float {
    0%,
    100% {
        transform: translateY(0px) rotate(0deg);
        opacity: 0.3;
    }
    50% {
        transform: translateY(-20px) rotate(180deg);
        opacity: 0.7;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .story-title {
        font-size: 1.8rem;
    }

    .story-text {
        font-size: 1rem;
    }

    .story-subtitle {
        font-size: 1.4rem;
    }

    .final-button {
        font-size: 1rem;
        padding: 0.8rem 1.5rem;
    }

    .final-message {
        font-size: 1.2rem;
    }
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}
</style>

<template>
    <div class="container my-4">
        <h2 class="text-center text-primary mb-3">
            📝 Bài Test {{ test.title }}
        </h2>

        <!-- TIMER -->
        <div class="alert alert-warning text-center fw-bold">
            ⏰ Thời gian còn lại: {{ minutes }}:{{ seconds }}
        </div>

        <!-- QUESTIONS -->
        <div v-for="(q, index) in questions" :key="q.id" class="card mb-3">
            <div class="card-body">
                <h5>Câu {{ index + 1 }}: {{ q.question }}</h5>

                <div v-for="opt in q.options" :key="opt.key" class="form-check">
                    <input class="form-check-input" type="radio" :name="'q' + q.id" :value="opt.key"
                        v-model="answers[q.id]" />
                    <label class="form-check-label">
                        {{ opt.key }}. {{ opt.text }}
                    </label>
                </div>
            </div>
        </div>

        <!-- SUBMIT -->
        <button class="btn btn-danger w-100 fw-bold" @click="submitTest">
            Nộp bài
        </button>
    </div>
</template>

<script>
import { getDetail, startTest, submitTest } from '@/api/testApi';

export default {
    props: {
        testId: { type: [Number, String], required: true },
    },
    data() {
        return {
            test: {},
            questions: [],
            answers: {},
            timeLeft: 900,
            timer: null,
        };
    },
    computed: {
        minutes() {
            return String(Math.floor(this.timeLeft / 60)).padStart(2, "0");
        },
        seconds() {
            return String(this.timeLeft % 60).padStart(2, "0");
        },
    },
    mounted() {
        this.loadTest();
    },
    beforeUnmount() {
        if (this.timer) clearInterval(this.timer);
    },
    methods: {
        async loadTest() {
            try {
                const res = await getDetail(this.testId);
                this.test = res.data;
                this.questions = res.data.questions || [];
                if (this.test.maxTime) {
                    this.timeLeft = this.test.maxTime * 60;
                }
                await startTest(this.testId);
                this.startTimer();
            } catch (err) {
                console.error('Lỗi tải bài test:', err);
            }
        },
        startTimer() {
            this.timer = setInterval(() => {
                this.timeLeft--;
                if (this.timeLeft <= 0) {
                    this.handleSubmit();
                }
            }, 1000);
        },
        async handleSubmit() {
            clearInterval(this.timer);
            try {
                const payload = this.questions.map(q => ({
                    id_cau_hoi: q.id,
                    id_dap_an: this.answers[q.id] || null,
                }));
                const res = await submitTest(this.testId, payload);
                alert('Hoàn thành! Điểm của bạn: ' + (res.data?.diem_tong ?? 'Chưa chấm'));
                this.$router.push('/');
            } catch (err) {
                console.error('Lỗi nộp bài:', err);
            }
        },
    },
};
</script>

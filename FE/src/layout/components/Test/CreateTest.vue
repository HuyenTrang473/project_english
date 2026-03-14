<template>
    <div class="container my-4">
        <h2 class="text-primary mb-4">👨‍🏫 Tạo bài test mới</h2>

        <input v-model="test.title" class="form-control mb-3" placeholder="Tên bài test" />

        <div v-for="(q, index) in test.questions" :key="index" class="card mb-3">
            <div class="card-body">
                <input v-model="q.question" class="form-control mb-2" placeholder="Câu hỏi" />

                <div v-for="opt in ['A', 'B', 'C', 'D']" :key="opt">
                    <input v-model="q.options[opt]" class="form-control mb-1" :placeholder="'Đáp án ' + opt" />
                </div>

                <select v-model="q.correct" class="form-control mt-2">
                    <option disabled value="">Đáp án đúng</option>
                    <option>A</option>
                    <option>B</option>
                    <option>C</option>
                    <option>D</option>
                </select>
            </div>
        </div>

        <button class="btn btn-secondary me-2" @click="addQuestion">
            ➕ Thêm câu hỏi
        </button>

        <button class="btn btn-primary" @click="saveTest">
            💾 Lưu bài test
        </button>
    </div>
</template>

<script>
import { createTest } from '@/api/testApi';

export default {
    data() {
        return {
            test: {
                title: "",
                questions: [],
            },
        };
    },
    methods: {
        addQuestion() {
            this.test.questions.push({
                question: "",
                options: { A: "", B: "", C: "", D: "" },
                correct: "",
            });
        },
        async saveTest() {
            try {
                await createTest(this.test);
                alert("Tạo bài test thành công");
                this.$router.push("/admin");
            } catch (err) {
                console.error('Lỗi tạo bài test:', err);
            }
        },
    },
};
</script>

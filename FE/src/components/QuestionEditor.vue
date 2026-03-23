<template>
  <div class="modal d-block fade show" tabindex="-1" style="background: rgba(0,0,0,0.5)">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <!-- Header -->
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">
            {{ question ? '✏️ Chỉnh Sửa Câu Hỏi' : '➕ Tạo Câu Hỏi Mới' }}
          </h5>
          <button @click="$emit('close')" type="button" class="btn-close btn-close-white"></button>
        </div>

        <!-- Body -->
        <div class="modal-body">
          <!-- Question Type -->
          <div class="mb-3">
            <label class="form-label">Loại Câu Hỏi *</label>
            <select v-model="form.loai_cau_hoi" class="form-select">
              <option value="multiple_choice">📋 Trắc Nghiệm (Multiple Choice)</option>
              <option value="true_false">✓✗ Đúng/Sai (True/False)</option>
              <option value="essay">📝 Tự Luận (Essay)</option>
              <option value="matching">🔗 Ghép Đôi (Matching)</option>
              <option value="fill_blank">___ Điền Chỗ Trống (Fill Blank)</option>
              <option value="image_choice">🖼️ Chọn Hình Ảnh (Image Choice)</option>
            </select>
          </div>

          <!-- Question Content -->
          <div class="mb-3">
            <label class="form-label">Nội Dung Câu Hỏi *</label>
            <textarea v-model="form.noi_dung" class="form-control" rows="3"
              placeholder="Nhập nội dung câu hỏi"></textarea>
          </div>

          <!-- Description -->
          <div class="mb-3">
            <label class="form-label">Mô Tả Chi Tiết</label>
            <textarea v-model="form.mo_ta_chi_tiet" class="form-control" rows="2"
              placeholder="Mô tả chi tiết hơn cho câu hỏi"></textarea>
          </div>

          <!-- Notes -->
          <div class="mb-3">
            <label class="form-label">Ghi Chú</label>
            <textarea v-model="form.ghi_chu" class="form-control" rows="2"
              placeholder="Ghi chú thêm (chỉ dành cho giáo viên)"></textarea>
          </div>

          <!-- Audio Upload for Listening Questions -->
          <div v-if="['listening', 'mixed'].includes(loaiQuiz)" class="mb-3">
            <label class="form-label">
              📁 Upload File Audio (.mp3)
              <span v-if="loaiQuiz === 'listening'" class="text-danger">*</span>
            </label>
            <div class="input-group">
              <input type="file" accept=".mp3" @change="handleAudioUpload" class="form-control"
                :id="`audio-upload-${Date.now()}`" ref="audioInput">
              <button v-if="form.audio_file_name" @click="removeAudio" class="btn btn-outline-danger" type="button">
                🗑️ Xóa
              </button>
            </div>
            <small class="form-text text-muted">
              Chỉ chấp nhận file MP3, dung lượng tối đa 50MB
            </small>
            <div v-if="form.audio_file_name" class="alert alert-info mt-2">
              <i class="fa fa-check"></i> File đã upload: <strong>{{ form.audio_file_name }}</strong> ({{
                formatFileSize(form.audio_file_size) }})
            </div>
            <div v-if="audioUploadError" class="alert alert-danger mt-2">
              <i class="fa fa-exclamation-circle"></i> {{ audioUploadError }}
            </div>
          </div>

          <!-- Answers Section -->
          <div class="mb-3">
            <label class="form-label">Đáp Án</label>

            <!-- Multiple Choice / True False / Image Choice -->
            <div v-if="['multiple_choice', 'true_false', 'image_choice'].includes(form.loai_cau_hoi)">
              <div v-for="(answer, index) in form.answers" :key="index" class="input-group mb-2">
                <span class="input-group-text">{{ String.fromCharCode(65 + index) }}:</span>
                <input v-model="answer.noi_dung" type="text" class="form-control" placeholder="Nội dung đáp án">
                <input v-if="form.loai_cau_hoi === 'image_choice'" v-model="answer.hinh_anh_url" type="url"
                  class="form-control" placeholder="URL hình ảnh">
                <div class="input-group-text">
                  <input v-model="answer.la_dap_an_dung" type="checkbox" title="Đáp án đúng?">
                </div>
                <button @click="removeAnswer(index)" type="button" class="btn btn-danger">
                  Xóa
                </button>
              </div>
              <button @click="addAnswer" type="button" class="btn btn-outline-primary btn-sm">
                <i class="fa fa-plus"></i> Thêm Đáp Án
              </button>
            </div>

            <!-- Matching -->
            <div v-else-if="form.loai_cau_hoi === 'matching'">
              <div v-for="(pair, index) in form.answers" :key="index" class="mb-2">
                <div class="row">
                  <div class="col-md-5">
                    <input v-model="pair.trai" type="text" class="form-control form-control-sm" placeholder="Cột trái">
                  </div>
                  <div class="col-md-5">
                    <input v-model="pair.phai" type="text" class="form-control form-control-sm" placeholder="Cột phải">
                  </div>
                  <div class="col-md-2">
                    <button @click="removeAnswer(index)" type="button" class="btn btn-danger btn-sm w-100">
                      Xóa
                    </button>
                  </div>
                </div>
              </div>
              <button @click="addMatchingPair" type="button" class="btn btn-outline-primary btn-sm">
                <i class="fa fa-plus"></i> Thêm Cặp
              </button>
            </div>

            <!-- Fill Blank -->
            <div v-else-if="form.loai_cau_hoi === 'fill_blank'">
              <div v-for="(answer, index) in form.answers" :key="index" class="input-group mb-2">
                <input v-model="answer.noi_dung" type="text" class="form-control" placeholder="Đáp án đúng">
                <button @click="removeAnswer(index)" type="button" class="btn btn-danger">
                  Xóa
                </button>
              </div>
              <p class="text-muted small mb-2">Có thể thêm multiple đáp án đúng</p>
              <button @click="addAnswer" type="button" class="btn btn-outline-primary btn-sm">
                <i class="fa fa-plus"></i> Thêm Đáp Án Đúng
              </button>
            </div>

            <!-- Essay -->
            <div v-else-if="form.loai_cau_hoi === 'essay'">
              <div class="alert alert-info">
                <strong>Câu tự luận</strong> sẽ được giáo viên chấm điểm thủ công.
                Hãy nhập tiêu chí chấm điểm hoặc câu trả lời mẫu.
              </div>
              <textarea v-model="form.dap_an_mau" class="form-control" rows="3"
                placeholder="Câu trả lời mẫu hoặc tiêu chí chấm điểm"></textarea>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <button @click="$emit('close')" type="button" class="btn btn-secondary">
            Hủy
          </button>
          <button @click="onSave" type="button" class="btn btn-primary">
            <i class="fa fa-save"></i> Lưu Câu Hỏi
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";

const props = defineProps({
  question: {
    type: Object,
    default: null,
  },
  loaiQuiz: {
    type: String,
    default: "mixed",
  },
});

const emit = defineEmits(["save", "close"]);

// Form
const form = reactive({
  loai_cau_hoi: "multiple_choice",
  noi_dung: "",
  mo_ta_chi_tiet: "",
  ghi_chu: "",
  answers: [],
  dap_an_mau: "",
  audio_url: "",
  audio_file_name: "",
  audio_file_size: 0,
});

const audioFile = ref(null);
const audioUploadError = ref("");
const removeAudioFlag = ref(false);

// Methods
const initializeAnswers = () => {
  if (!form.answers || form.answers.length === 0) {
    if (form.loai_cau_hoi === "matching") {
      form.answers = [
        { trai: "", phai: "" },
        { trai: "", phai: "" },
      ];
    } else if (form.loai_cau_hoi === "essay") {
      form.answers = [];
    } else if (form.loai_cau_hoi === "image_choice") {
      form.answers = [
        { noi_dung: "", hinh_anh_url: "", la_dap_an_dung: false },
        { noi_dung: "", hinh_anh_url: "", la_dap_an_dung: false },
      ];
    } else {
      form.answers = [
        { noi_dung: "", la_dap_an_dung: false },
        { noi_dung: "", la_dap_an_dung: false },
      ];
    }
  }
};

const addAnswer = () => {
  if (form.loai_cau_hoi === "fill_blank") {
    form.answers.push({ noi_dung: "" });
  } else if (form.loai_cau_hoi === "image_choice") {
    form.answers.push({
      noi_dung: "",
      hinh_anh_url: "",
      la_dap_an_dung: false
    });
  } else {
    form.answers.push({
      noi_dung: "",
      la_dap_an_dung: false
    });
  }
};

const addMatchingPair = () => {
  form.answers.push({ trai: "", phai: "" });
};

const removeAnswer = (index) => {
  form.answers.splice(index, 1);
};

const formatFileSize = (bytes) => {
  if (!bytes) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
};

const handleAudioUpload = (event) => {
  const file = event.target.files[0];
  if (!file) return;

  audioUploadError.value = '';
  removeAudioFlag.value = false;

  // Validate file type
  if (file.type !== 'audio/mpeg' && !file.name.endsWith('.mp3')) {
    audioUploadError.value = 'Chỉ chấp nhận file MP3';
    return;
  }

  // Validate file size (50MB)
  const maxSize = 50 * 1024 * 1024;
  if (file.size > maxSize) {
    audioUploadError.value = 'Dung lượng file không được vượt quá 50MB';
    return;
  }

  audioFile.value = file;
  form.audio_file_name = file.name;
  form.audio_file_size = file.size;
};

const removeAudio = () => {
  audioFile.value = null;
  form.audio_url = "";
  form.audio_file_name = '';
  form.audio_file_size = 0;
  audioUploadError.value = '';
  removeAudioFlag.value = true;
  const fileInput = document.querySelector(`input[type="file"]`);
  if (fileInput) fileInput.value = '';
};

const onSave = () => {
  if (!form.noi_dung) {
    alert("Vui lòng nhập nội dung câu hỏi");
    return;
  }

  // Validate answers based on type
  if (form.loai_cau_hoi !== "essay") {
    if (!form.answers || form.answers.length < 2) {
      alert("Vui lòng thêm ít nhất 2 đáp án");
      return;
    }

    if (["multiple_choice", "true_false", "image_choice"].includes(form.loai_cau_hoi)) {
      if (!form.answers.some(a => a.la_dap_an_dung)) {
        alert("Vui lòng chọn ít nhất một đáp án đúng");
        return;
      }
    }
  }

  emit("save", {
    ...form,
    _audioFile: audioFile.value,
    _removeAudio: removeAudioFlag.value,
  });
};

// Lifecycle
onMounted(() => {
  if (props.question) {
    Object.assign(form, props.question);
  }
  initializeAnswers();
});
</script>

<style scoped>
.modal.show {
  animation: fadeIn 0.2s ease-in;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.input-group-text {
  background-color: #f8f9fa;
  min-width: 60px;
}

.img-thumbnail {
  object-fit: cover;
}
</style>

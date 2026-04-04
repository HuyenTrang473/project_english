<template>
  <div class="modal d-block fade show" tabindex="-1" style="background: rgba(0,0,0,0.5)">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <!-- Header -->
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">
            <i :class="question ? 'fa fa-pencil' : 'fa fa-plus-circle'"></i>
            {{ question ? ' Chỉnh Sửa Câu Hỏi' : ' Tạo Câu Hỏi Mới' }}
          </h5>
          <button @click="$emit('close')" type="button" class="btn-close btn-close-white"></button>
        </div>

        <!-- Body -->
        <div class="modal-body">
          <!-- Question Type -->
          <div class="mb-3">
            <label class="form-label">Loại Câu Hỏi *</label>
            <select v-model="form.loai_cau_hoi" class="form-select">
              <option value="multiple_choice">Trắc Nghiệm (Multiple Choice)</option>
              <option value="true_false">Đúng/Sai (True/False)</option>
              <option value="essay">Tự Luận (Essay)</option>
              <option value="matching">Ghép Đôi (Matching)</option>
              <option value="fill_blank">___ Điền Chỗ Trống (Fill Blank)</option>
              <option value="image_choice">Chọn Hình Ảnh (Image Choice)</option>
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

          <!-- Point Value -->
          <div class="mb-3">
            <label class="form-label">Điểm Tối Đa <span class="text-danger">*</span></label>
            <input v-model.number="form.diem_toi_da" type="number" class="form-control" min="0.1" step="0.1"
              placeholder="Điểm tối đa cho câu hỏi này">
          </div>

          <!-- Audio Upload for Listening Questions -->
          <div v-if="loaiQuiz === 'listening'" class="mb-3">
            <label class="form-label">
              <i class="fa fa-headphones"></i> File Audio (<span class="text-danger">Bắt Buộc</span>)
            </label>
            <div class="audio-upload-section">
              <div class="input-group mb-2">
                <input type="file" accept=".mp3,.wav,.ogg,audio/mpeg,audio/wav,audio/ogg" @change="handleAudioUpload"
                  class="form-control" :id="`audio-upload-${Date.now()}`" ref="audioInput" :disabled="audioUploading">
                <button v-if="form.audio_file_name && form.audio_url" @click="removeAudio"
                  class="btn btn-outline-danger" type="button" :disabled="audioUploading">
                  <i class="fa fa-trash"></i> Xóa
                </button>
              </div>
              <small class="form-text text-muted d-block mb-2">
                Chỉ chấp nhận: MP3, WAV, OGG | Dung lượng tối đa: 50MB
              </small>

              <!-- Audio Preview -->
              <div v-if="form.audio_url && !audioUploading" class="alert alert-success mb-2">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <i class="fa fa-music"></i> <strong>{{ form.audio_file_name }}</strong><br>
                    <small>{{ formatFileSize(form.audio_file_size) }}</small>
                  </div>
                  <audio controls :src="form._audioPreviewUrl || form.audio_url" class="audio-preview"></audio>
                </div>
              </div>

              <!-- Upload Progress -->
              <div v-if="audioUploading" class="progress mb-2">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                  style="width: 100%">
                  <i class="fa fa-upload"></i> Đang tải audio...
                </div>
              </div>

              <!-- Error Messages -->
              <div v-if="audioUploadError" class="alert alert-danger mb-0">
                <i class="fa fa-exclamation-circle"></i> {{ audioUploadError }}
              </div>
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
  diem_toi_da: 1,  // ✅ ADD: Point value for the question
  audio_url: "",
  audio_file_name: "",
  audio_file_size: 0,
});

const audioFile = ref(null);
const audioUploading = ref(false);
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

  // Validate file type - support mp3, wav, ogg
  const validTypes = ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/x-wav'];
  const validExtensions = ['mp3', 'wav', 'ogg'];
  const extension = file.name.split('.').pop().toLowerCase();

  if (!validTypes.includes(file.type) && !validExtensions.includes(extension)) {
    audioUploadError.value = 'Chỉ chấp nhận file audio: MP3, WAV, OGG';
    event.target.value = ''; // Reset input
    return;
  }

  // Validate file size (50MB)
  const maxSize = 50 * 1024 * 1024;
  if (file.size > maxSize) {
    audioUploadError.value = 'Dung lượng file không được vượt quá 50MB';
    event.target.value = ''; // Reset input
    return;
  }

  audioFile.value = file;
  form.audio_file_name = file.name;
  form.audio_file_size = file.size;
  // ✅ FIX: Only create blob:// for preview, keep audio_url for storage reference
  // Store the preview separately - don't pollute audio_url with blob://
  form._audioPreviewUrl = URL.createObjectURL(file);
  console.log('Audio file selected:', file.name, 'Preview URL:', form._audioPreviewUrl);
};

const removeAudio = () => {
  audioFile.value = null;
  // ✅ FIX: Revoke both blob URLs properly
  if (form._audioPreviewUrl && form._audioPreviewUrl.startsWith('blob:')) {
    URL.revokeObjectURL(form._audioPreviewUrl);
  }
  if (form.audio_url && form.audio_url.startsWith('blob:')) {
    URL.revokeObjectURL(form.audio_url);
  }
  form._audioPreviewUrl = "";
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

  // Validate audio for listening quiz
  if (props.loaiQuiz === 'listening') {
    // Check if has audio (either existing or being uploaded)
    const hasAudio = form.audio_url || audioFile.value;
    if (!hasAudio && !form.audio_file_name) {
      alert("Bài test listening phải có file audio cho mỗi câu hỏi. Vui lòng tải lên file audio.");
      return;
    }
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
    // ✅ FIX: Only send actual audio_url (storage path), not blob://
    // Remove preview URL before sending to backend
    _audioFile: audioFile.value,
    _removeAudio: removeAudioFlag.value,
    _audioPreviewUrl: undefined, // Don't send preview URL to backend
  });
  console.log('Question saved:', { noi_dung: form.noi_dung, diem_toi_da: form.diem_toi_da, answerCount: form.answers.length });
};

// Lifecycle
onMounted(() => {
  if (props.question) {
    // ✅ FIX: Deep copy to preserve all properties including IDs
    Object.assign(form, {
      ...props.question,
      // Ensure diem_toi_da is preserved or defaults to 1
      diem_toi_da: props.question.diem_toi_da ?? props.question.diem_max ?? 1,
      // Separate preview URL from storage URL
      _audioPreviewUrl: props.question.audio_url && !props.question.audio_url.startsWith('blob:')
        ? props.question.audio_url
        : "",
    });
    console.log('Question loaded:', form);
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

/* Audio Upload Section */
.audio-upload-section {
  padding: 0;
}

.audio-preview {
  max-width: 300px;
  height: 32px;
  border-radius: 4px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.audio-preview::-webkit-media-controls-panel {
  background-color: #f8f9fa;
}

.audio-upload-section .form-control:disabled,
.audio-upload-section .btn:disabled {
  opacity: 0.6;
}

/* Audio file input styling */
.audio-upload-section input[type="file"]::-webkit-file-upload-button {
  background-color: #007bff;
  color: white;
  padding: 6px 16px;
  border: none;
  border-radius: 4px 0 0 4px;
  cursor: pointer;
  font-weight: 500;
}

.audio-upload-section input[type="file"]::-webkit-file-upload-button:hover {
  background-color: #0056b3;
}
</style>

# Audio Feature for Listening Questions - Complete Guide

## Overview

This document provides a comprehensive guide for the Audio feature implementation in the test creation and taking system. This feature allows listening questions to have audio files associated with them and provides proper playback during test taking.

## Table of Contents

1. [Database Design](#database-design)
2. [Backend Implementation](#backend-implementation)
3. [Frontend Implementation](#frontend-implementation)
4. [API Endpoints](#api-endpoints)
5. [File Storage](#file-storage)
6. [Best Practices](#best-practices)
7. [Troubleshooting](#troubleshooting)

---

## Database Design

### Schema

The `cau_hois` (questions) table includes the following audio-related columns:

```sql
-- Audio URL - path to the stored audio file
audio_url: string (nullable)

-- Original audio filename (for display purposes)
audio_file_name: string (nullable)

-- File size in bytes (for validation and UI display)
audio_file_size: bigInteger (nullable)
```

### Migration

Created in: `database/migrations/2026_03_16_000003_add_audio_to_cau_hois_table.php`

### Model (CauHoi.php)

The model includes these fields in `$fillable`:

- `audio_url`
- `audio_file_name`
- `audio_file_size`

---

## Backend Implementation

### 1. Request Validation (StoreCauHoiRequest.php)

**Key Validation Rules:**

- Listening questions **MUST** have an audio file
- Supports multiple audio formats: MP3, WAV, OGG
- Maximum file size: 50MB
- Custom validation ensures listening questions cannot be saved without audio

**Usage Example:**

```php
// When submitting a listening question, the validation will:
// 1. Check if question type is 'listening'
// 2. Verify that either a new audio file is uploaded OR existing audio_url is present
// 3. Validate file format and size
```

### 2. Controller Methods (CauHoiController.php)

#### Store Method

Creates a new question with audio:

```php
POST /api/tests/{testId}/questions

// Request with FormData:
{
  'noi_dung': 'Question text',
  'loai_cau_hoi': 'listening',
  'audio_file': <File>, // FormData file object
  'diem_max': 1
}

// Response (201 Created):
{
  'success': true,
  'message': 'Tạo câu hỏi thành công',
  'data': {
    'id': 123,
    'audio_url': '/storage/audio/questions/question_123_1234567890.mp3',
    'audio_file_name': 'question_123_1234567890.mp3',
    'audio_file_size': 2048000,
    ...
  }
}
```

#### Update Method

Updates an existing question:

```php
PUT /api/tests/{testId}/questions/{questionId}

// Can upload new audio (old audio automatically deleted):
{
  'audio_file': <File> // New audio file
}

// Or remove audio:
{
  '_removeAudio': true
}
```

#### Audio Upload Handler

Private method `handleQuestionAudioUpload()`:

- Validates file format (MP3, WAV, OGG)
- Validates file size (max 50MB)
- Deletes old audio file if exists
- Stores new file with pattern: `question_{id}_{timestamp}.{ext}`
- Updates database with new audio URL and metadata

#### Audio Removal Handler

Private method `removeQuestionAudio()`:

- Deletes audio file from storage
- Clears audio fields in database

---

## Frontend Implementation

### 1. Question Editor Component (QuestionEditor.vue)

**Audio Upload Section** (only visible for listening questions):

```vue
<div v-if="form.loai_cau_hoi === 'listening'" class="mb-3">
  <!-- File input for audio -->
  <!-- Audio preview with player -->
  <!-- Upload progress indicator -->
  <!-- Error messages -->
</div>
```

**Features:**

- Conditional rendering: audio upload only shown for listening questions
- File validation before upload (type and size)
- Preview audio player with controls
- Error handling and user feedback
- Audio removal capability
- Progress indicator during validation

**Validation Logic:**

```javascript
// Only listening questions can have audio
if (form.loai_cau_hoi === "listening") {
  // Audio is required
  const hasAudio = form.audio_url || audioFile.value;
  if (!hasAudio) {
    // Show error: "Câu hỏi listening phải có file audio"
  }
}

// File type validation
const validFormats = ["audio/mpeg", "audio/wav", "audio/ogg"];
const validExtensions = ["mp3", "wav", "ogg"];

// File size validation (50MB)
const maxSize = 50 * 1024 * 1024;
```

### 2. Test Taking Component (DoTest.vue)

**Audio Player Section:**

```vue
<div v-if="question.audio_url" class="audio-section">
  <audio ref="audioPlayerRef" :src="question.audio_url" controls />
  <button @click="playAudio" class="audio-btn">
    🔊 Phát Âm Thanh
  </button>
</div>
```

**Features:**

- Native HTML5 audio player with controls
- Play button for convenience
- Prevents simultaneous audio playback (stops other audio when playing new one)
- Responsive design
- Proper audio cleanup on component unmount

**Simultaneous Playback Prevention:**

```javascript
const playAudio = () => {
  // Stop all other audio elements
  stopAllAudio();

  // Play current audio
  audioPlayerRef.value.play();
};

const stopAllAudio = () => {
  const allAudioElements = document.querySelectorAll("audio");
  allAudioElements.forEach((audio) => {
    if (!audio.paused) {
      audio.pause();
      audio.currentTime = 0;
    }
  });
};
```

---

## API Endpoints

### Create Question with Audio

```http
POST /api/tests/{testId}/questions
Content-Type: multipart/form-data

Parameters:
- noi_dung (required): Question text
- loai_cau_hoi (required): 'listening', 'multiple_choice', etc.
- audio_file (required for listening): Audio file (MP3, WAV, OGG, max 50MB)
- diem_max (optional): Max points

Response:
{
  "success": true,
  "message": "Tạo câu hỏi thành công",
  "data": {
    "id": 123,
    "noi_dung": "Listen to the audio...",
    "loai_cau_hoi": "listening",
    "audio_url": "/storage/audio/questions/question_123_1234567890.mp3",
    "audio_file_name": "question_123_1234567890.mp3",
    "audio_file_size": 2048000,
    "diem_max": 1,
    "dap_ans": []
  }
}
```

### Update Question Audio

```http
PUT /api/tests/{testId}/questions/{questionId}
Content-Type: multipart/form-data

// Upload new audio:
{
  "audio_file": <File>
}

// Remove audio:
{
  "_removeAudio": true
}

Response:
{
  "success": true,
  "message": "Cập nhật câu hỏi thành công",
  "data": { ... }
}
```

### Get Test Details (with audio)

```http
GET /api/tests/{testId}

Response includes:
{
  "data": {
    "questions": [
      {
        "id": 123,
        "noi_dung": "Listen to the audio",
        "loai_cau_hoi": "listening",
        "audio_url": "/storage/audio/questions/question_123_1234567890.mp3",
        "answers": [ ... ]
      },
      ...
    ]
  }
}
```

---

## File Storage

### Directory Structure

```
storage/
└── app/
    └── public/
        └── audio/
            └── questions/
                ├── question_123_1234567890.mp3
                ├── question_124_1234567891.wav
                └── question_125_1234567892.ogg
```

### File Naming Convention

Pattern: `question_{question_id}_{unix_timestamp}.{extension}`

Example: `question_42_1711428450.mp3`

**Advantages:**

- Unique filenames prevent overwrites
- Question ID for easy tracking
- Timestamp for chronological organization
- Extension preserves format

### URL Generation

Files are served through Laravel's storage URL:

```
/storage/audio/questions/question_123_1234567890.mp3
```

Public access is configured in `config/filesystems.php`:

```php
'public' => [
    'driver' => 'local',
    'path' => 'public',
    'visibility' => 'public',
    'url' => env('APP_URL').'/storage',
],
```

### Storage Cleanup

When a question is updated with a new audio file:

1. Old audio file is deleted from storage
2. Database is updated with new audio information

When a question is deleted:

1. Associated audio file is automatically deleted (via model observer)
2. Database record is removed

---

## Best Practices

### 1. Validation

```php
// ✅ DO: Validate at multiple levels
// Request validation (StoreCauHoiRequest)
// Controller validation (business logic)
// Frontend validation (user experience)

// ❌ DON'T: Only validate on frontend
// Backend validation must be comprehensive
```

### 2. File Handling

```javascript
// ✅ DO: Clean up blob URLs
if (form.audio_url && form.audio_url.startsWith("blob:")) {
  URL.revokeObjectURL(form.audio_url);
}

// ✅ DO: Stop audio on component unmount
onUnmounted(() => {
  stopAllAudio();
});

// ❌ DON'T: Leave blob URLs unreleased
// ❌ DON'T: Leave audio playing when navigating away
```

### 3. Error Handling

```php
// ✅ DO: Provide meaningful error messages
'Câu hỏi listening phải có file audio. Vui lòng tải lên file audio.'

// ❌ DON'T: Generic error messages
// 'Error: File upload failed'

// ✅ DO: Log errors for debugging
Log::error('Error uploading question audio: ' . $e->getMessage());
```

### 4. User Experience

```vue
<!-- ✅ DO: Show progress and status -->
<div v-if="audioUploading" class="progress">
  <div class="progress-bar progress-bar-animated">
    📤 Đang tải audio...
  </div>
</div>

<!-- ✅ DO: Provide audio preview -->
<audio controls :src="form.audio_url"></audio>

<!-- ❌ DON'T: Provide no feedback during upload -->
<!-- ❌ DON'T: Make users guess if audio is present -->
```

### 5. Performance

- Maximum file size: 50MB per audio file
- Supported formats: MP3, WAV, OGG (compressed formats recommended)
- Consider using MP3 for best browser compatibility
- Pre-compress audio files before upload

---

## Troubleshooting

### Issue: Audio File Not Playing

**Cause:** Incorrect file path in database
**Solution:**

```bash
# Check storage/app/public/audio/questions/ directory
# Verify audio_url in database matches actual file path
# Ensure public symlink exists: php artisan storage:link
```

### Issue: "Listening questions must have audio" error

**Cause:** Question is being created without audio file
**Solution:**

- Ensure audio file is selected before saving
- Check file format is MP3, WAV, or OGG
- Check file size is under 50MB

### Issue: Old Audio File Not Deleted

**Cause:** Storage path issue or file permissions
**Solution:**

```bash
# Check storage permissions
chmod -R 755 storage/app/public/audio

# Verify path format in database
# Example correct path: /storage/audio/questions/question_123_timestamp.mp3
```

### Issue: Simultaneous Audio Playback

**Cause:** Multiple audio elements playing at once
**Solution:**

- The `stopAllAudio()` function in DoTest.vue should handle this
- Check browser console for JavaScript errors
- Verify `<audio controls>` elements exist in DOM

### Issue: CORS Error When Loading Audio

**Cause:** Cross-origin request blocked
**Solution:**

- Ensure audio files are served from same domain
- Check CORS headers if serving from CDN
- Verify file permissions allow public access

### Audio Upload Fails Silently

**Cause:** File validation failing but error not shown
**Solution:**

```javascript
// Add validation feedback
if (!validTypes.includes(file.type)) {
  audioUploadError.value = "Invalid file format";
  console.error("Audio validation failed:", {
    type: file.type,
    name: file.name,
    size: file.size,
  });
}
```

---

## Testing

### Backend Testing

```php
// Test audio upload for listening question
$response = $this->postJson('/api/tests/1/questions', [
    'noi_dung' => 'Listen to this',
    'loai_cau_hoi' => 'listening',
    'audio_file' => UploadedFile::fake()->create('test.mp3', 1000, 'audio/mpeg'),
]);

$response->assertStatus(201)
    ->assertJsonPath('data.audio_file_name', 'question_*');

// Test validation - listening without audio should fail
$response = $this->postJson('/api/tests/1/questions', [
    'noi_dung' => 'Listen to this',
    'loai_cau_hoi' => 'listening',
    // No audio_file provided
]);

$response->assertStatus(422)
    ->assertJsonValidationErrors(['audio_file']);
```

### Frontend Testing

```javascript
// Test audio validation
const file = new File(["audio data"], "test.mp3", { type: "audio/mpeg" });
const event = { target: { files: [file] } };
handleAudioUpload(event);

expect(audioFile.value).toBeDefined();
expect(audioUploadError.value).toBe("");

// Test invalid file
const invalidFile = new File(["data"], "test.txt", { type: "text/plain" });
handleAudioUpload({ target: { files: [invalidFile] } });

expect(audioUploadError.value).toContain("MP3, WAV, OGG");
```

---

## Security Considerations

1. **File Type Validation**
   - Validate both MIME type and file extension
   - Check magic bytes for audio files

2. **File Size Limits**
   - Set maximum size: 50MB
   - Validate on both client and server

3. **Storage Permissions**
   - Store files outside public web root if possible
   - Or ensure proper Access-Control headers

4. **Disk Space**
   - Monitor storage usage
   - Implement cleanup for deleted questions

5. **Access Control**
   - Only teachers can upload/manage audio
   - Only students taking the test can access audio

---

## Future Enhancements

1. **Audio Transcription**
   - Auto-generate transcripts using AWS Transcribe or similar
   - Help accessibility

2. **Audio Streaming**
   - Use streaming protocol for large files
   - Better bandwidth management

3. **Multiple Audio Tracks**
   - Support multiple versions (normal, slow, etc.)
   - Enhanced learning experience

4. **Audio Preview Before Save**
   - Waveform visualization
   - Trim/edit functionality

5. **Compression**
   - Auto-compress uploaded files
   - Reduce storage requirements

---

## References

- Laravel Storage Documentation: https://laravel.com/docs/storage
- HTML5 Audio API: https://developer.mozilla.org/en-US/docs/Web/HTML/Element/audio
- Vue 3 File Upload: https://vuejs.org/guide/essentials/forms.html#file-input-binding
- Audio Format Support: https://caniuse.com/audio

---

**Last Updated:** March 26, 2026
**Version:** 1.0
**Status:** Production Ready ✅

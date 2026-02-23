@extends('depootcom.admin.blog.layout')

@section('title', 'Edit Artikel')
@section('page_title', 'Edit Post')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="flex items-center space-x-4">
        <a href="{{ route('depootcom.admin.blog.index') }}" class="w-10 h-10 rounded-full bg-white shadow-sm border border-slate-200 flex items-center justify-center text-slate-500 hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h3 class="text-3xl font-black text-slate-800">Edit Artikel</h3>
    </div>

    <form action="{{ route('depootcom.admin.blog.update', $blog) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-2">Judul Artikel</label>
                    <input type="text" name="title" value="{{ $blog->title }}" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:outline-none focus:border-blue-500 transition-colors font-bold text-lg">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-2">Kategori</label>
                    <select name="category_id" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:outline-none focus:border-blue-500 transition-colors font-bold text-lg appearance-none">
                        <option value="">Tanpa Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $blog->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-2">Featured Image</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <div class="relative group h-40 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl flex flex-col items-center justify-center overflow-hidden transition-all hover:border-blue-400">
                        <input type="file" name="featured_image" id="featured_image" class="absolute inset-0 opacity-0 cursor-pointer z-10" onchange="previewImage(this)">
                        <img id="preview-placeholder" src="{{ $blog->featured_image ? asset('storage/' . $blog->featured_image) : '' }}" class="absolute inset-0 w-full h-full object-cover {{ $blog->featured_image ? '' : 'hidden' }}">
                        <div id="upload-hint" class="flex flex-col items-center text-slate-400 group-hover:text-blue-500 transition-colors {{ $blog->featured_image ? 'hidden' : '' }}">
                            <i class="fas fa-cloud-upload-alt text-3xl mb-2"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest">Ganti Gambar Utama</span>
                        </div>
                    </div>
                    <div class="text-[10px] text-slate-400 font-medium italic">
                        *Gambar saat ini akan tetap digunakan jika tidak mengupload gambar baru. Gunakan format JPG/PNG, ukuran maksimal 2MB.
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-2">Ringkasan (Optional)</label>
                <textarea name="excerpt" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:outline-none focus:border-blue-500 transition-colors font-medium">{{ $blog->excerpt }}</textarea>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between ml-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Konten Artikel</label>
                    <button type="button" id="btn-generate-ai" class="flex items-center space-x-2 text-[10px] font-black text-blue-600 hover:text-blue-700 uppercase tracking-widest transition-colors">
                        <i class="fas fa-robot"></i>
                        <span>Tulis dengan AI</span>
                    </button>
                </div>

                <!-- Tiptap Toolbar -->
                <div id="tiptap-toolbar" class="flex flex-wrap gap-1 p-2 bg-slate-100 rounded-t-2xl border-x border-t border-slate-200">
                    <button type="button" data-action="toggleBold" class="p-2 rounded hover:bg-slate-200 transition-colors"><i class="fas fa-bold"></i></button>
                    <button type="button" data-action="toggleItalic" class="p-2 rounded hover:bg-slate-200 transition-colors"><i class="fas fa-italic"></i></button>
                    <button type="button" data-action="toggleH2" class="p-2 rounded hover:bg-slate-200 transition-colors"><i class="fas fa-heading"></i><span class="text-[10px] ml-1">2</span></button>
                    <button type="button" data-action="toggleH3" class="p-2 rounded hover:bg-slate-200 transition-colors"><i class="fas fa-heading"></i><span class="text-[10px] ml-1">3</span></button>
                    <div class="w-px h-6 bg-slate-300 mx-1 self-center"></div>
                    <button type="button" data-action="toggleBulletList" class="p-2 rounded hover:bg-slate-200 transition-colors"><i class="fas fa-list-ul"></i></button>
                    <button type="button" data-action="toggleOrderedList" class="p-2 rounded hover:bg-slate-200 transition-colors"><i class="fas fa-list-ol"></i></button>
                    <button type="button" data-action="toggleBlockquote" class="p-2 rounded hover:bg-slate-200 transition-colors"><i class="fas fa-quote-right"></i></button>
                    <div class="w-px h-6 bg-slate-300 mx-1 self-center"></div>
                    <button type="button" data-action="addImage" class="p-2 rounded hover:bg-slate-200 transition-colors text-blue-500"><i class="fas fa-image"></i></button>
                    <div class="w-px h-6 bg-slate-300 mx-1 self-center"></div>
                    <button type="button" data-action="undo" class="p-2 rounded hover:bg-slate-200 transition-colors"><i class="fas fa-undo"></i></button>
                    <button type="button" data-action="redo" class="p-2 rounded hover:bg-slate-200 transition-colors"><i class="fas fa-redo"></i></button>
                </div>

                <div id="tiptap-editor" class="w-full bg-slate-50 border border-slate-200 rounded-b-2xl px-6 py-8 focus-within:border-blue-500 transition-colors font-medium leading-relaxed min-h-[400px]"></div>
                
                <textarea name="content" id="content-textarea" class="hidden" required>{{ $blog->content }}</textarea>
                <input type="file" id="image-upload-input" accept="image/*" class="hidden">
                <p class="text-[10px] text-slate-400 ml-2 italic">*Editor Tiptap aktif. Klik ikon gambar untuk mengupload atau memasukkan URL gambar.</p>
            </div>

            <div class="flex items-center justify-between p-6 bg-slate-50 rounded-[2.5rem] border border-slate-100">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-blue-100 text-blue-600 rounded-2xl">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm">Status Penerbitan</h4>
                        <p class="text-[10px] text-slate-400 font-medium">Aktifkan untuk menampilkan artikel di publik.</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_published" id="is_published" {{ $blog->is_published ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-14 h-8 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-5 rounded-2xl font-black uppercase text-sm tracking-widest shadow-xl shadow-blue-500/20 transition-all">
                Update Postingan
            </button>
        </div>
    </form>
</div>

<style>
    .ProseMirror { outline: none; min-height: 350px; }
    .ProseMirror p { margin-bottom: 1em; }
    .ProseMirror h2 { font-size: 1.5rem; font-weight: 700; margin: 1.5em 0 0.5em; }
    .ProseMirror h3 { font-size: 1.25rem; font-weight: 700; margin: 1.25em 0 0.5em; }
    .ProseMirror ul { list-style-type: disc; padding-left: 1.5em; margin-bottom: 1em; }
    .ProseMirror ol { list-style-type: decimal; padding-left: 1.5em; margin-bottom: 1em; }
    .ProseMirror blockquote { border-left: 4px solid #e2e8f0; padding-left: 1em; font-style: italic; color: #64748b; margin-bottom: 1em; }
</style>

@vite('resources/js/tiptap.js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.setupTiptap('#tiptap-editor', '#content-textarea', '{{ route('depootcom.admin.upload-image') }}', '{{ csrf_token() }}');

        const btnAi = document.getElementById('btn-generate-ai');
        btnAi.addEventListener('click', async () => {
            const topic = document.querySelector('input[name="title"]').value;
            if (!topic) {
                alert('Tolong masukkan judul terlebih dahulu sebagai topik AI.');
                return;
            }

            if (!confirm(`Hasilkan konten AI untuk topik: "${topic}"? Konten yang ada akan ditimpa.`)) return;

            btnAi.disabled = true;
            btnAi.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Menghasilkan...</span>';
            
            try {
                const response = await fetch('{{ route('depootcom.admin.blog.generate-ai') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ topic: topic })
                });

                const data = await response.json();

                if (data.error) {
                    alert('Error: ' + data.error);
                } else if (data.html) {
                    if (window.editor) {
                        window.editor.commands.setContent(data.html);
                    } else {
                        const editorEl = document.querySelector('.ProseMirror');
                        if (editorEl) editorEl.innerHTML = data.html;
                    }
                    alert('Konten AI berhasil dihasilkan!');
                }
            } catch (error) {
                console.error('AI Error:', error);
                alert('Terjadi kesalahan saat menghubungi server AI.');
            } finally {
                btnAi.disabled = false;
                btnAi.innerHTML = '<i class="fas fa-robot"></i><span>Tulis dengan AI</span>';
            }
        });
    });

    function previewImage(input) {
        const preview = document.getElementById('preview-placeholder');
        const hint = document.getElementById('upload-hint');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                hint.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection

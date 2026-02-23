import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';

// Expose Editor to global scope if needed, or initialize here based on DOM
// Ideally, we make a function available globally to init editor
window.setupTiptap = function (elementId, textareaId, uploadUrl, csrfToken) {
    const editor = new Editor({
        element: document.querySelector(elementId),
        extensions: [
            StarterKit,
            Image.configure({
                HTMLAttributes: {
                    class: 'max-w-full h-auto rounded-2xl my-4',
                },
            }),
        ],
        content: document.querySelector(textareaId).value || '',
        onUpdate({ editor }) {
            document.querySelector(textareaId).value = editor.getHTML();
        },
        onTransaction({ editor }) {
            // Update toolbar states
            const toolbar = document.querySelector('#tiptap-toolbar');
            if (!toolbar) return;

            const buttons = toolbar.querySelectorAll('button');
            buttons.forEach(btn => {
                const action = btn.getAttribute('data-action');
                if (!action) return;

                if (action === 'toggleBold' && editor.isActive('bold')) btn.classList.add('bg-blue-100', 'text-blue-600');
                else if (action === 'toggleItalic' && editor.isActive('italic')) btn.classList.add('bg-blue-100', 'text-blue-600');
                else if (action === 'toggleH2' && editor.isActive('heading', { level: 2 })) btn.classList.add('bg-blue-100', 'text-blue-600');
                else if (action === 'toggleH3' && editor.isActive('heading', { level: 3 })) btn.classList.add('bg-blue-100', 'text-blue-600');
                else if (action === 'toggleBulletList' && editor.isActive('bulletList')) btn.classList.add('bg-blue-100', 'text-blue-600');
                else if (action === 'toggleOrderedList' && editor.isActive('orderedList')) btn.classList.add('bg-blue-100', 'text-blue-600');
                else if (action === 'toggleBlockquote' && editor.isActive('blockquote')) btn.classList.add('bg-blue-100', 'text-blue-600');
                else btn.classList.remove('bg-blue-100', 'text-blue-600');
            });
        }
    });

    // Button Listeners
    const toolbar = document.querySelector('#tiptap-toolbar');
    if (toolbar) {
        const buttons = toolbar.querySelectorAll('button');
        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                const action = btn.getAttribute('data-action');
                if (!action) return;

                if (action === 'toggleBold') editor.chain().focus().toggleBold().run();
                if (action === 'toggleItalic') editor.chain().focus().toggleItalic().run();
                if (action === 'toggleH2') editor.chain().focus().toggleHeading({ level: 2 }).run();
                if (action === 'toggleH3') editor.chain().focus().toggleHeading({ level: 3 }).run();
                if (action === 'toggleBulletList') editor.chain().focus().toggleBulletList().run();
                if (action === 'toggleOrderedList') editor.chain().focus().toggleOrderedList().run();
                if (action === 'toggleBlockquote') editor.chain().focus().toggleBlockquote().run();
                if (action === 'undo') editor.chain().focus().undo().run();
                if (action === 'redo') editor.chain().focus().redo().run();

                if (action === 'addImage') {
                    const choice = window.confirm('Klik OK untuk Upload Gambar dari komputer, atau Cancel untuk memasukkan URL Gambar.')

                    if (choice) {
                        const fileInput = document.querySelector('#image-upload-input');
                        fileInput.click();

                        fileInput.onchange = async () => {
                            if (fileInput.files.length > 0) {
                                const file = fileInput.files[0];
                                const formData = new FormData();
                                formData.append('image', file);
                                formData.append('_token', csrfToken);

                                try {
                                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                                    btn.disabled = true;

                                    const response = await fetch(uploadUrl, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': csrfToken,
                                            'Accept': 'application/json'
                                        },
                                        body: formData
                                    });

                                    const data = await response.json();
                                    if (data.url) {
                                        editor.chain().focus().setImage({ src: data.url }).run();
                                    } else {
                                        alert('Upload gagal: ' + (data.error || 'Terjadi kesalahan'));
                                    }
                                } catch (error) {
                                    alert('Upload gagal: ' + error.message);
                                } finally {
                                    btn.innerHTML = '<i class="fas fa-image"></i>';
                                    btn.disabled = false;
                                    fileInput.value = '';
                                }
                            }
                        };
                    } else {
                        const url = window.prompt('Masukkan URL Gambar:')
                        if (url) {
                            editor.chain().focus().setImage({ src: url }).run()
                        }
                    }
                }
            });
        });
    }

    return editor;
};

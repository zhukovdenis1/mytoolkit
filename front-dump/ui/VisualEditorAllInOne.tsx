import './tiptap.css';
import {
    BubbleMenu,
    EditorContent,
    useEditor,
} from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import React, { useState } from 'react';

// Define the props type
type VisualEditorProps = {
    value: string;
    onChange: (value: string) => void;
    disabled?: boolean;
};

const VisualEditorAllInOne: React.FC<VisualEditorProps> = ({ value, onChange, disabled }) => {
    disabled; // чтобы не ругался на неиспользуемый параметр, можно просто в теле не использовать
    const [isLinkModalOpen, setLinkModalOpen] = useState(false);
    const [linkData, setLinkData] = useState({
        href: '',
        target: '_self',
        class: '',
    });

    const editor = useEditor({
        extensions: [
            StarterKit,
            Link.configure({
                openOnClick: false, // чтобы не сразу открывалось
                HTMLAttributes: {
                    target: '_blank', // по умолчанию все ссылки с target="_blank"
                },
            }),
        ],
        content: value,
        onUpdate: ({ editor }) => onChange(editor.getHTML()),
    });

    const openLinkModal = () => {
        if (!editor) return;

        const { href = '', target = '_self', class: className = '' } = editor.getAttributes('link');

        setLinkData({ href, target, class: className });
        setLinkModalOpen(true);
    };

    const applyLink = () => {
        const attributes: Record<string, string> = {
            href: linkData.href,
            target: linkData.target,
            class: linkData.class,
        };

        if (linkData.target === '_blank') {
            attributes.rel = 'nofollow noindex';
        }

        if (linkData.href) {
            editor?.chain().focus().setLink(attributes).run();
        } else {
            editor?.chain().focus().unsetLink().run();
        }

        setLinkModalOpen(false);
    };

    const removeLink = () => {
        editor?.chain().focus().unsetLink().run();
        setLinkModalOpen(false);
    };

    return (
        <div className="visual-editor-container">
            {editor && (
                <BubbleMenu className="bubble-menu" tippyOptions={{ duration: 100 }} editor={editor}>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleBold().run()}
                        className={editor.isActive('bold') ? 'is-active' : ''}
                    >
                        <b>B</b>
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleItalic().run()}
                        className={editor.isActive('italic') ? 'is-active' : ''}
                    >
                        <i>I</i>
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleStrike().run()}
                        className={editor.isActive('strike') ? 'is-active' : ''}
                    >
                        <s>S</s>
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleBulletList().run()}
                        className={editor.isActive('bulletList') ? 'is-active' : ''}
                    >
                        *List
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleCode().run()}
                        className={editor.isActive('code') ? 'is-active' : ''}
                    >
                        &lt;&gt;
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleHeading({ level: 2 }).run()}
                        className={editor.isActive('heading', { level: 2 }) ? 'is-active' : ''}
                    >
                        H2
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleHeading({ level: 3 }).run()}
                        className={editor.isActive('heading', { level: 3 }) ? 'is-active' : ''}
                    >
                        H3
                    </button>
                    <button
                        type="button"
                        onClick={openLinkModal}
                        className={editor.isActive('link') ? 'is-active' : ''}
                    >
                        🔗 Link
                    </button>
                </BubbleMenu>
            )}

            <div className="visual-editor-content">
                <EditorContent editor={editor} />
            </div>

            {/* Модалка для ссылки */}
            {isLinkModalOpen && (
                <div className="link-modal">
                    <div className="modal-content">
                        <h3>Настройки ссылки</h3>
                        <label>
                            URL:
                            <input
                                type="text"
                                value={linkData.href}
                                onChange={(e) => setLinkData({ ...linkData, href: e.target.value })}
                            />
                        </label>
                        <label>
                            Target:
                            <select
                                value={linkData.target}
                                onChange={(e) => setLinkData({ ...linkData, target: e.target.value })}
                            >
                                <option value="_self">Открыть в текущем окне (_self)</option>
                                <option value="_blank">Открыть в новой вкладке (_blank)</option>
                                <option value="_parent">В родительском окне (_parent)</option>
                                <option value="_top">Во всём окне (_top)</option>
                            </select>
                        </label>
                        <label>
                            CSS Class:
                            <input
                                type="text"
                                value={linkData.class}
                                onChange={(e) => setLinkData({ ...linkData, class: e.target.value })}
                            />
                        </label>
                        <div className="modal-actions">
                            <button onClick={applyLink}>Сохранить</button>
                            {editor?.isActive('link') && (
                                <button onClick={removeLink} style={{ marginLeft: 'auto', color: 'red' }}>
                                    Удалить ссылку
                                </button>
                            )}
                            <button onClick={() => setLinkModalOpen(false)}>Отмена</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};


export default VisualEditor;

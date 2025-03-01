import '@/css/ui/tiptap.css';
import {
    BubbleMenu,
    EditorContent,
    useEditor,
} from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import React, { useState } from 'react';
import LinkModal from './LinkModal';
import { buildLinkAttributes } from './LinkHelpers';

import type { VisualEditorProps, LinkData } from './types';

const VisualEditor: React.FC<VisualEditorProps> = ({ value, onChange, disabled, mode }) => {
    disabled;
    const [isLinkModalOpen, setLinkModalOpen] = useState(false);
    const [linkData, setLinkData] = useState<LinkData>({
        href: '',
        target: '_self',
        class: '',
    });

    const editor = useEditor({
        extensions: [
            StarterKit,
            Link.configure({ openOnClick: (mode === 'view') }),
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

    const applyLink = (data: LinkData) => {
        const attributes = buildLinkAttributes(data);
        if (data.href) {
            editor?.chain().focus().setLink(attributes).run();
        } else {
            editor?.chain().focus().unsetLink().run();
        }
        setLinkModalOpen(false);
    };

    return (
        <div className="visual-editor-container">
            {editor && (
                <BubbleMenu className="bubble-menu" tippyOptions={{duration: 100, maxWidth: 400}} editor={editor}>
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
                        onClick={() => editor.chain().focus().toggleHeading({level: 2}).run()}
                        className={editor.isActive('heading', {level: 2}) ? 'is-active' : ''}
                    >
                        H2
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleHeading({level: 3}).run()}
                        className={editor.isActive('heading', {level: 3}) ? 'is-active' : ''}
                    >
                        H3
                    </button>
                    <button
                        type="button"
                        onClick={openLinkModal}
                        className={editor.isActive('link') ? 'is-active' : ''}
                    >
                        🔗
                    </button>
                </BubbleMenu>
            )}

            <div className="visual-editor-content">
                <EditorContent editor={editor}/>
            </div>

            {isLinkModalOpen && (
                <LinkModal
                    initialData={linkData}
                    onSave={applyLink}
                    onCancel={() => setLinkModalOpen(false)}
                    onUnlink={() => {
                        editor?.chain().focus().unsetLink().run();
                        setLinkModalOpen(false);
                    }}
                    isEditingLink={editor?.isActive('link') ?? false}
                />
            )}
        </div>
    );
};

export default VisualEditor;

import './tiptap.css';
import {
    BubbleMenu,
    EditorContent,
    useEditor,
} from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import React, { useState } from 'react';
import { html } from "js-beautify"; // Импортируем нужный метод
import CodeEditor from "@/components/ui/editor/CodeEditor";

const VisualEditor = ({ value, onChange, disabled }) => {
    const [isSourceView, setIsSourceView] = useState(false);

    const editor = useEditor({
        extensions: [
            StarterKit,
        ],
        content: value,
        onUpdate: ({ editor }) => onChange(editor.getHTML())
    })

    const formatCode = (code: string): string => {
        try {
            return html(code, {
                indent_size: 2,         // Размер отступов
                max_preserve_newlines: 1, // Максимальное количество переносов строк
                preserve_newlines: true,  // Сохранение переносов
            });
        } catch (error) {
            console.error('Error formatting code:', error);
            return code;  // Возвращаем исходный код в случае ошибки
        }
    };

    const toggleSourceView = () => {
        setIsSourceView((prev) => !prev);
        // Обновляем состояние редактора
        console.log(editor?.getHTML());
        setTimeout(() => {
            editor?.commands.focus();
            editor?.commands.setContent(editor?.getHTML())
        }, 0);
    };

    return (
        <div className="editor-container">
            {editor && <BubbleMenu className="bubble-menu" tippyOptions={{duration: 100}} editor={editor}>
                <button type="button"
                        onClick={() => editor.chain().focus().toggleBold().run()}
                        className={editor.isActive('bold') ? 'is-active' : ''}
                >
                    <b>B</b>
                </button>
                <button type="button"
                        onClick={() => editor.chain().focus().toggleItalic().run()}
                        className={editor.isActive('italic') ? 'is-active' : ''}
                >
                    <i>I</i>
                </button>
                <button type="button"
                        onClick={() => editor.chain().focus().toggleStrike().run()}
                        className={editor.isActive('strike') ? 'is-active' : ''}
                >
                    <strike>S</strike>
                </button>
                <button type="button"
                        onClick={() => editor.chain().focus().toggleBulletList().run()}
                        className={editor.isActive('bulletList') ? 'is-active' : ''}
                >
                    *List
                </button>
                <button type="button"
                        onClick={() => editor.chain().focus().toggleCode().run()}
                        className={editor.isActive('code') ? 'is-active' : ''}
                >
                    &lt;&gt;
                </button>
                <button type="button"
                        onClick={() => editor.chain().focus().toggleHeading({level: 2}).run()}
                        className={editor.isActive({level:2}) ? 'is-active' : ''}
                >
                    H2
                </button>
                <button type="button"
                        onClick={() => editor.chain().focus().toggleHeading({level: 3}).run()}
                        className={editor.isActive('H3') ? 'is-active' : ''}
                >
                    H3
                </button>
            </BubbleMenu>}

            <button className="editor-btn source-toggle" onClick={toggleSourceView} type="button">
                {isSourceView ? 'Visual' : 'Source'}
            </button>

            <div className="editor-content">
                {isSourceView ? (
                    <CodeEditor
                        value={formatCode(editor.getHTML())}
                        onChange={(val) => editor.commands.setContent(val)}
                        type="php"
                    />
                ) : (
                    <EditorContent editor={editor} />
                )}
            </div>
        </div>
    )
}

export default VisualEditor;

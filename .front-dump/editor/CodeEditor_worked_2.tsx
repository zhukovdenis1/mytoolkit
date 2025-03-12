import React, { useEffect, useRef } from "react";
import { EditorView, keymap, lineNumbers, highlightActiveLine } from "@codemirror/view";
import { EditorState, Transaction } from "@codemirror/state";
import { indentWithTab, history, defaultKeymap } from "@codemirror/commands";
import { autocompletion, closeBrackets } from "@codemirror/autocomplete";
import { syntaxHighlighting, defaultHighlightStyle } from "@codemirror/language";
import { html } from "@codemirror/lang-html";
import { javascript } from "@codemirror/lang-javascript";
import { css } from "@codemirror/lang-css";
import { php } from "@codemirror/lang-php";
import { markdown } from "@codemirror/lang-markdown";
import { oneDark } from "@codemirror/theme-one-dark";

interface CodeEditorProps {
    value: string;
    onChange?: (value: string) => void;
}

const CodeEditor: React.FC<CodeEditorProps> = ({ value, onChange }) => {
    const editorRef = useRef<HTMLDivElement>(null);
    const viewRef = useRef<EditorView | null>(null);

    useEffect(() => {
        if (!editorRef.current) return;

        if (!viewRef.current) {
            const extensions = [
                keymap.of([...defaultKeymap, indentWithTab]),
                history(),
                autocompletion(),
                closeBrackets(),
                lineNumbers(),
                highlightActiveLine(),
                syntaxHighlighting(defaultHighlightStyle),
                // oneDark,
                html({ matchClosingTags: true, autoCloseTags: true }),
                php(),
                javascript(),
                css(),
                markdown(),
                EditorView.updateListener.of((update) => {
                    if (update.docChanged && onChange) {
                        onChange(update.state.doc.toString());
                    }
                }),
            ];

            const state = EditorState.create({
                doc: value,
                extensions,
            });

            viewRef.current = new EditorView({
                state,
                parent: editorRef.current,
            });
        }
    }, []);

    useEffect(() => {
        if (viewRef.current) {
            const currentDoc = viewRef.current.state.doc.toString();
            if (value !== currentDoc) {
                const selection = viewRef.current.state.selection.ranges[0]; // Получаем текущую позицию курсора

                const transaction = viewRef.current.state.update({
                    changes: { from: 0, to: currentDoc.length, insert: value },
                    selection: { anchor: selection.anchor, head: selection.head } // Восстанавливаем позицию курсора
                });

                viewRef.current.dispatch(transaction);
            }
        }
    }, [value]);

    return <div ref={editorRef} className="codemirror-container"></div>;
};

export default CodeEditor;

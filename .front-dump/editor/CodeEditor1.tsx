import React, { useEffect, useRef } from "react";
import { EditorView, keymap, lineNumbers, highlightActiveLine } from "@codemirror/view";
import { EditorState/*, Compartment */} from "@codemirror/state";
import { indentWithTab, history, defaultKeymap } from "@codemirror/commands";
import { autocompletion, closeBrackets } from "@codemirror/autocomplete";
import { syntaxHighlighting, defaultHighlightStyle } from "@codemirror/language";
import { html } from "@codemirror/lang-html";
import { javascript } from "@codemirror/lang-javascript";
import { css } from "@codemirror/lang-css";
import { php } from "@codemirror/lang-php";
import { markdown } from "@codemirror/lang-markdown";
import { indentUnit } from "@codemirror/language"; // Настройка отступов

interface CodeEditorProps {
    value: string;
    onChange?: (value: string) => void;
}

const CodeEditor: React.FC<CodeEditorProps> = ({ value, onChange }) => {
    const editorRef = useRef<HTMLDivElement>(null);
    const viewRef = useRef<EditorView | null>(null);

    useEffect(() => {
        if (!editorRef.current) return;

        //const language = new Compartment();

        if (!viewRef.current) {
            const extensions = [
                //language.of([php()]),
                keymap.of([...defaultKeymap, indentWithTab]),
                history(),
                autocompletion(),
                closeBrackets(),
                lineNumbers(),
                highlightActiveLine(),
                syntaxHighlighting(defaultHighlightStyle),
                indentUnit.of("    "), // Устанавливаем отступы в 4 пробела
                php(),
                html(),
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
                doc: value || "", // Защита от undefined
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
            const currentDoc = viewRef.current.state.doc.toString() || ""; // Гарантируем строку
            if (typeof value !== "string") return; // Проверяем, что value — это строка

            if (value !== currentDoc) {
                const selection = viewRef.current.state.selection?.main;

                // Проверяем, что selection существует и корректен
                const newAnchor = selection && typeof selection.anchor === "number" ? Math.min(selection.anchor, value.length) : 0;
                const newHead = selection && typeof selection.head === "number" ? Math.min(selection.head, value.length) : 0;

                const transaction = viewRef.current.state.update({
                    changes: { from: 0, to: currentDoc.length, insert: value },
                    selection: { anchor: newAnchor, head: newHead }
                });

                viewRef.current.dispatch(transaction);
            }
        }
    }, [value]);


    return <div ref={editorRef} className="codemirror-container"></div>;
};

export default CodeEditor;

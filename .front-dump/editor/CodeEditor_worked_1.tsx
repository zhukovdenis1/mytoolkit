import React, { useEffect, useRef } from "react";
import { EditorView, keymap, lineNumbers, highlightActiveLine } from "@codemirror/view";
import { EditorState, Compartment } from "@codemirror/state";
import { indentWithTab, history, defaultKeymap } from "@codemirror/commands";
import { autocompletion, closeBrackets } from "@codemirror/autocomplete";
import { syntaxHighlighting, defaultHighlightStyle } from "@codemirror/language";
import { javascript } from "@codemirror/lang-javascript";
import { html } from "@codemirror/lang-html";
import { css } from "@codemirror/lang-css";
import { php } from "@codemirror/lang-php";
import { xml } from "@codemirror/lang-xml";
import { java } from "@codemirror/lang-java";
import { oneDark } from "@codemirror/theme-one-dark";

interface CodeEditorProps {
    value: string;
    onChange?: (value: string) => void;
    language?: "javascript" | "html" | "css" | "php" | "xml" | "java";
}

const CodeEditor: React.FC<CodeEditorProps> = ({ value, onChange, language = "javascript" }) => {
    const editorRef = useRef<HTMLDivElement>(null);
    const viewRef = useRef<EditorView | null>(null);
    const languageCompartment = useRef(new Compartment()).current;

    // Функция обновления содержимого редактора
    const updateValue = (newValue: string) => {
        const view = viewRef.current;
        if (!view) return;

        const transaction = view.state.update({
            changes: { from: 0, to: view.state.doc.length, insert: newValue },
        });

        view.dispatch(transaction);
    };

    // Функция обновления языка подсветки
    const updateLanguage = (newLanguage: string) => {
        const view = viewRef.current;
        if (!view) return;

        const langExtension = newLanguage === "javascript" ? javascript()
            : newLanguage === "html" ? html()
                : newLanguage === "css" ? css()
                    : newLanguage === "php" ? php()
                        : newLanguage === "xml" ? xml()
                            : newLanguage === "java" ? java()
                                : [];

        view.dispatch({
            effects: languageCompartment.reconfigure(langExtension),
        });
    };

    useEffect(() => {
        if (!editorRef.current) return;

        const extensions = [
            keymap.of([...defaultKeymap, indentWithTab]),
            history(),
            autocompletion(),
            closeBrackets(),
            lineNumbers(),
            highlightActiveLine(),
            syntaxHighlighting(defaultHighlightStyle),
            oneDark,
            languageCompartment.of(javascript()), // Инициализируем с JavaScript, потом обновим
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

        const view = new EditorView({
            state,
            parent: editorRef.current,
        });

        viewRef.current = view;

        return () => {
            view.destroy();
        };
    }, []);

    // Следим за изменением `value` и обновляем редактор
    useEffect(() => {
        if (viewRef.current && value !== viewRef.current.state.doc.toString()) {
            updateValue(value);
        }
    }, [value]);

    // Следим за изменением `language` и обновляем подсветку
    useEffect(() => {
        updateLanguage(language);
    }, [language]);

    return <div ref={editorRef} className="codemirror-container"></div>;
};

export default CodeEditor;

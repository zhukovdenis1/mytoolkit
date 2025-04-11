import CodeMirror from '@uiw/react-codemirror';
import { html } from "@codemirror/lang-html";
import { javascript } from "@codemirror/lang-javascript";
import { css } from "@codemirror/lang-css";
import { php } from "@codemirror/lang-php";
import { json } from "@codemirror/lang-json";
import { sql } from "@codemirror/lang-sql";
import { LanguageSupport } from "@codemirror/language"; // Импортируем LanguageSupport

export type CodeEditorData = {
    text: string;
    language: string
}

interface CodeEditorProps {
    data: CodeEditorData;
    onChange: (data: CodeEditorData) => void;
    disabled: boolean;
    mode: string;
}

function CodeEditor({data, onChange, disabled }: CodeEditorProps) {

    let extensions: LanguageSupport[] = [];

    switch (data.language) {
        case 'html':
            extensions.push(html());
            break;
        case 'css':
            extensions.push(css());
            break;
        case 'js':
            extensions.push(javascript({ jsx: true }));
            break;
        case 'php':
            extensions.push(php());
            break;
        case 'json':
            extensions.push(json());
            break;
        case 'sql':
            extensions.push(sql());
            break;
        default:
            extensions.push(html());
            break;
    }

    return <CodeMirror
        value={data.text}
        // height="200px"
        extensions={extensions}
        onChange={(value) => onChange({text: value, language: data.language})}
        readOnly={disabled}
    />;
}

export default CodeEditor;

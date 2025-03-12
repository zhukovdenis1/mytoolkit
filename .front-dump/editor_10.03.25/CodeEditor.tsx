import CodeMirror from '@uiw/react-codemirror';
import { html } from "@codemirror/lang-html";
import { javascript } from "@codemirror/lang-javascript";
import { css } from "@codemirror/lang-css";
import { php } from "@codemirror/lang-php";
import { LanguageSupport } from "@codemirror/language"; // Импортируем LanguageSupport

interface CodeEditorProps {
    value: string;
    onChange: (value: string) => void;
    type: string;
    disabled: boolean;
}

function CodeEditor({value, onChange, type, disabled }: CodeEditorProps) {
    //const [value, setValue] = React.useState("");
    // const onChange = React.useCallback((val, viewUpdate) => {
    //     console.log('val:', val);
    //     setValue(val);
    // }, []);
    let extensions: LanguageSupport[] = [];

    switch (type) {
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
        default:
            extensions.push(html());
            break;
    }

    return <CodeMirror
        value={value}
        // height="200px"
        extensions={extensions}
        onChange={onChange}
        readOnly={disabled}
    />;
}

export default CodeEditor;

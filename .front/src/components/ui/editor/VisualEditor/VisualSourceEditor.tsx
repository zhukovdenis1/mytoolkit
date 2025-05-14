import CodeMirror from '@uiw/react-codemirror';
import { php } from "@codemirror/lang-php";
import { LanguageSupport } from "@codemirror/language"; // Импортируем LanguageSupport

export type VisualSourceEditorData = {
    text: string;
}

interface VisualSourceEditorProps {
    data: VisualSourceEditorData;
    onChange: (data: VisualSourceEditorData) => void;
    disabled: boolean;
    mode: string;
}

function VisualSourceEditor({data, onChange, disabled }: VisualSourceEditorProps) {

    let extensions: LanguageSupport[] = [];
    extensions.push(php());

    return <CodeMirror
        value={data.text}
        // height="200px"
        extensions={extensions}
        onChange={(value) => onChange({text: value})}
        readOnly={disabled}
    />;
}

export default VisualSourceEditor;

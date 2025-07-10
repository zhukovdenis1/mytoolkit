import { Spin as MySpin, SpinProps } from "antd";

const Spin = (props: SpinProps) => <MySpin size="large" {...props} />;

export {
    Button,
    Form,
    Input,
    Checkbox,
    message,
    Select,
    Space,
    Table,
    Tree,
    TreeSelect,
    Upload,
    Dropdown,
    Menu,
    DatePicker
} from "antd";

export { Spin };
export { default as Editor } from "@/components/ui/editor/Editor";
export { showModal } from "@/components/ui/Modal";
export { Confirmable } from "./Confirmable";
export { SearchInput } from "./SearchInput";
export { ButtonLink } from "./ButtonLink";

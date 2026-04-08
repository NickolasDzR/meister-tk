declare module '@nice-select2' {
    interface NiceSelectOptions {
        data?: any[];
        searchable?: boolean;
        showSelectedItems?: boolean;
        placeholder?: string;
        searchtext?: string;
        selectedtext?: string;
        afterUpdated?: (dropdown: HTMLElement) => void;
        onClickedSelect?: (dropdown: HTMLElement) => void;
        onClickedItem?: (item: HTMLElement) => void;
        onElementClicked?: () => void;
        onSearchInputChanged?: (input: HTMLInputElement) => void;
    }

    class NiceSelect {
        constructor(element: HTMLSelectElement, options?: NiceSelectOptions);
        update(): void;
        destroy(): void;
        disable(): void;
        enable(): void;
        clear(): void;
        setValue(value: string | string[]): void;
        getValue(): string | string[];
    }

    function bind(element: HTMLSelectElement, options?: NiceSelectOptions): NiceSelect;

    export default NiceSelect;
    export { bind };
}
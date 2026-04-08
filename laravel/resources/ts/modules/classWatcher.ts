import { capitalizeFirstLetter } from "@utils";

type EventType = 'tag' | 'class';

interface ObserverSettings {
    attributes?: boolean;
    childList?: boolean;
    characterData?: boolean;
    subtree?: boolean;
}

export class ClassWatcher {
    eventType: EventType;
    targetNode: HTMLElement;
    classToWatch: string;
    classAddedCallback: () => void;
    classRemovedCallback?: (() => void) | null;
    observer: MutationObserver | null;
    lastClassState: boolean;
    observerSettings: ObserverSettings;

    constructor(
        eventType: EventType,
        targetNode: HTMLElement,
        classToWatch: string,
        classAddedCallback: () => void,
        classRemovedCallback: (() => void) | null = null
    ) {
        this.eventType = eventType;
        this.targetNode = targetNode;
        this.classToWatch = classToWatch;
        this.classAddedCallback = classAddedCallback;
        this.classRemovedCallback = classRemovedCallback;
        this.observer = null;
        this.lastClassState = targetNode.classList.contains(this.classToWatch);
        this.observerSettings = {
            attributes: true
        };

        if (eventType === 'tag') {
            this.observerSettings.childList = true;
            this.observerSettings.characterData = false;
            this.observerSettings.subtree = true;
        }

        this.init();
    }

    init(): void {
        const callbackName = `mutationCallback${capitalizeFirstLetter(this.eventType)}`;
        const callback = (this as any)[callbackName]?.bind(this);

        if (callback) {
            this.observer = new MutationObserver(callback);
            this.observe();
        }
    }

    observe(): void {
        if (this.observer) {
            this.observer.observe(this.targetNode, this.observerSettings);
        }
    }

    disconnect(): void {
        if (this.observer) {
            this.observer.disconnect();
        }
    }

    mutationCallbackTag(_mutationsList: MutationRecord[]): void {
        if (this.targetNode.querySelector(".nice-select")) {
            this.classAddedCallback();
            this.disconnect();
        }
    }

    mutationCallbackClass(mutationsList: MutationRecord[]): void {
        for (const mutation of mutationsList) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                const target = mutation.target as HTMLElement;
                const currentClassState = target.classList.contains(this.classToWatch);

                if (this.lastClassState !== currentClassState) {
                    this.lastClassState = currentClassState;

                    if (currentClassState) {
                        this.classAddedCallback();
                    } else {
                        if (this.classRemovedCallback) {
                            this.classRemovedCallback();
                        }
                    }
                }
            }
        }
    }
}
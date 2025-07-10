import gsap from 'gsap'

export function onBeforeEnter(el: Element): void {
    const htmlEl: HTMLElement = el as HTMLElement
    htmlEl.style.opacity = '0'
}

export function onEnter(el: Element, done: () => void): void {
    const htmlEl: HTMLElement = el as HTMLElement
    gsap.to(el, {
        opacity: 1,
        delay: Number(htmlEl?.dataset?.index) * 0.05,
        onComplete: done
    })
}
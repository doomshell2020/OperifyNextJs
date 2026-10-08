"use client";
import React, { useEffect, useId, useRef, useState } from 'react';

type Props = React.InputHTMLAttributes<HTMLInputElement> & { list: string };
/** Uses the existing datalist data with the PHP autocomplete's plain suggestion menu. */
export function LegacyAutocompleteInput({ list, onChange, onFocus, onBlur, onKeyDown, ...props }: Props) {
  const ref = useRef<HTMLInputElement>(null);
  const id = useId();
  const [options, setOptions] = useState<string[]>([]);
  const [open, setOpen] = useState(false);
  const [active, setActive] = useState(-1);
  const readOptions = () => {
    const values = Array.from((document.getElementById(list) as HTMLDataListElement | null)?.options || []).map(option => option.value);
    const search = (ref.current?.value || '').toLowerCase();
    setOptions([...new Set(values)].filter(value => value.toLowerCase().includes(search)));
  };
  useEffect(() => {
    const data = document.getElementById(list);
    if (!data) return;
    const observer = new MutationObserver(() => { if (ref.current === document.activeElement) readOptions(); });
    observer.observe(data, { childList: true, subtree: true, attributes: true });
    return () => observer.disconnect();
  }, [list]);
  const select = (value: string) => {
    const input = ref.current;
    if (!input) return;
    input.value = value;
    onChange?.({ target: input, currentTarget: input, type: 'change' } as React.ChangeEvent<HTMLInputElement>);
    setOpen(false); setActive(-1);
  };
  return <span className="legacy-autocomplete">
    <input {...props} ref={ref} autoComplete="off" role="combobox" aria-autocomplete="list" aria-expanded={open && options.length > 0} aria-controls={id} aria-activedescendant={active >= 0 ? `${id}-${active}` : undefined}
      onFocus={event => { readOptions(); setOpen(true); onFocus?.(event); }}
      onBlur={event => { setOpen(false); onBlur?.(event); }}
      onChange={event => { onChange?.(event); readOptions(); setActive(-1); setOpen(true); }}
      onKeyDown={event => {
        if (event.key === 'Escape') setOpen(false);
        else if (open && options.length && (event.key === 'ArrowDown' || event.key === 'ArrowUp')) {
          event.preventDefault(); setActive(index => event.key === 'ArrowDown' ? Math.min(index + 1, options.length - 1) : Math.max(index - 1, 0));
        } else if (event.key === 'Enter' && open && active >= 0 && options[active]) { event.preventDefault(); select(options[active]); }
        onKeyDown?.(event);
      }}/>
    {open && options.length > 0 && <ul id={id} role="listbox" className="legacy-autocomplete-menu">
      {options.map((option, index) => <li key={option} id={`${id}-${index}`} role="option" aria-selected={index === active}
        onMouseDown={event => { event.preventDefault(); select(option); }} onMouseEnter={() => setActive(index)}>{option}</li>)}
    </ul>}
  </span>;
}

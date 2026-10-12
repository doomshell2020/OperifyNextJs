'use client';
import {useEffect,useRef,useState} from 'react';
import {createPortal} from 'react-dom';
import {ChevronDown} from 'lucide-react';

// Select2-style searchable choice used by the PHP Receive Add form.
export default function ReceiveSelect({options,value,onChange,placeholder,label}:{
  options:{id:string|number;name:string}[];value:string;onChange:(value:string)=>void;placeholder:string;label:string;
}) {
  const [open,setOpen]=useState(false),[search,setSearch]=useState('');
  const root=useRef<HTMLDivElement>(null);
  const menu=useRef<HTMLDivElement>(null);
  const [position,setPosition]=useState({top:0,left:0,width:0});
  useEffect(()=>{const close=(event:MouseEvent)=>{if(root.current && !root.current.contains(event.target as Node) && !menu.current?.contains(event.target as Node))setOpen(false);};
    document.addEventListener('mousedown',close);return()=>document.removeEventListener('mousedown',close);},[]);
  const selected=options.find(option=>String(option.id)===value);
  const filtered=options.filter(option=>option.name.toLowerCase().includes(search.toLowerCase()));
  return <div ref={root} className="jc-receive-select">
    <button type="button" role="combobox" aria-label={label} aria-expanded={open} aria-haspopup="listbox"
      onClick={()=>{const box=root.current?.getBoundingClientRect();if(box)setPosition({top:box.bottom,left:box.left,width:box.width});setSearch('');setOpen(!open);}} onKeyDown={event=>{if(event.key==='Escape')setOpen(false);}}>
      <span>{selected?.name || placeholder}</span><span className="jc-receive-select-arrow"><ChevronDown size={12}/></span>
    </button>
    {open && createPortal(<div ref={menu} className="jc-receive-select-menu" style={{position:'fixed',...position}}>
      <input autoFocus type="search" aria-label={`Search ${label}`} value={search} onChange={event=>setSearch(event.target.value)} onKeyDown={event=>{
        if(event.key==='Escape')setOpen(false);
        if(event.key==='Enter'){
          event.preventDefault();
          if(filtered[0]){onChange(String(filtered[0].id));setOpen(false);}
        }
      }}/>
      <div role="listbox" aria-label={label}>
        <button type="button" role="option" aria-selected={!value} onClick={()=>{onChange('');setOpen(false);}}>{placeholder}</button>
        {filtered.map(option=><button type="button" role="option" aria-selected={String(option.id)===value} key={option.id}
          onClick={()=>{onChange(String(option.id));setOpen(false);}}>{option.name}</button>)}
        {!filtered.length && <p className="p-2">No results found</p>}
      </div>
    </div>,document.body)}
  </div>;
}

'use client';

import {useEffect, useRef} from 'react';
import {DesignSheetDetails} from './DesignSheetDetails';

export function DesignSheetDetailsModal({id, onClose}: {id: string; onClose: () => void}) {
  const dialogRef = useRef<HTMLDialogElement>(null);

  useEffect(() => {
    const dialog = dialogRef.current;
    dialog?.showModal();
    const previousOverflow = document.body.style.overflow;
    document.body.style.overflow = 'hidden';
    return () => {
      dialog?.close();
      document.body.style.overflow = previousOverflow;
    };
  }, []);

  return <dialog
    ref={dialogRef}
    aria-label="Design Sheet Details"
    className="fixed inset-0 m-auto w-[calc(100%-2rem)] max-w-5xl max-h-[90vh] overflow-y-auto rounded bg-[#ecf0f5] p-5 text-[#333] shadow-xl backdrop:bg-black/40 select-text"
    // Handle Escape directly; cleanup also fires native close events in Strict Mode.
    onCancel={event => {
      event.preventDefault();
      onClose();
    }}
    onClick={event => {
      if (event.target === event.currentTarget) {
        const bounds = event.currentTarget.getBoundingClientRect();
        if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) onClose();
      }
    }}
  >
    <button type="button" aria-label="Close design sheet details" className="absolute right-3 top-2 px-2 text-2xl" onClick={onClose}>×</button>
    <DesignSheetDetails id={id} onClose={onClose} />
  </dialog>;
}

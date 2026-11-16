'use client';

import Link, {useLinkStatus} from 'next/link';
import {ReactNode} from 'react';

function LinkContent({children}:{children:ReactNode}) {
  const {pending}=useLinkStatus();
  return <span aria-busy={pending}>{pending?'Opening…':children}</span>;
}

export function DailySheetLink({href,children,className='legacy-button'}:{href:string;children:ReactNode;className?:string}) {
  return <Link href={href} className={className}><LinkContent>{children}</LinkContent></Link>;
}

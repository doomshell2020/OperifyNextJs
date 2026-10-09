import {NextResponse} from 'next/server';

// Existing files remain on disk for the authenticated backend compatibility
// resolver. Do not expose them through Next's public-file serving.
export function proxy() {
  return new NextResponse('Use the Design Sheet download action.', {status:404});
}
export const config = {matcher:'/designsheet/:path*'};

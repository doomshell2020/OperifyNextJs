import { NextRequest, NextResponse } from 'next/server';

export const dynamic = 'force-dynamic';

// Keep the existing download URL, using the same template as GRN's Print action.
export async function GET(request: NextRequest, {params}: {params:Promise<{id:string}>}) {
  const {id} = await params;
  if (!/^\d+$/.test(id)) return NextResponse.json({error:'Invalid GRN ID'}, {status:400});
  try {
    const apiUrl = process.env.API_URL || process.env.NEXT_PUBLIC_API_URL || 'http://localhost:5000/api';
    const response = await fetch(`${apiUrl.replace(/\/$/,'')}/grn/${id}/pdf`, {
      headers: {Authorization:request.headers.get('authorization') || ''}, cache:'no-store',
    });
    if (!response.ok) {
      const failure = await response.json().catch(() => null);
      const message = failure?.error?.message || failure?.message ||
        (typeof failure?.error === 'string' ? failure.error : 'Unable to generate GRN PDF');
      console.error('GRN PDF backend request failed', { id, status: response.status, message });
      return NextResponse.json({ error: message, code: failure?.error?.code }, { status: response.status });
    }
    if (!response.headers.get('content-type')?.includes('application/pdf')) {
      console.error('GRN PDF backend returned a non-PDF response', { id, status: response.status });
      return NextResponse.json({ error: 'The PDF server returned an unexpected response' }, { status: 502 });
    }
    return new NextResponse(response.body, {headers:{
      'Content-Type':'application/pdf',
      'Content-Disposition':`attachment; filename="GRN_Details_${id}.pdf"`,
      'Cache-Control':'no-store',
    }});
  } catch (error) {
    console.error('GRN PDF backend connection failed', error);
    return NextResponse.json({error:'Failed to generate PDF'}, {status:500});
  }
}

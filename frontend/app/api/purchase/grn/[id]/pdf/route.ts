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
    if (!response.ok) return NextResponse.json({error:'Unable to generate GRN PDF'}, {status:response.status});
    return new NextResponse(response.body, {headers:{
      'Content-Type':'application/pdf',
      'Content-Disposition':`attachment; filename="GRN_Details_${id}.pdf"`,
      'Cache-Control':'no-store',
    }});
  } catch {
    return NextResponse.json({error:'Failed to generate PDF'}, {status:500});
  }
}

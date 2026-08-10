import { NextRequest, NextResponse } from 'next/server';
import React from 'react';
import { renderToStream } from '@react-pdf/renderer';
import { GrnPdf } from '@/components/purchase/grn/GrnPdf';

// Important: ensure dynamic route
export const dynamic = 'force-dynamic';

export async function GET(
  request: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id } = await params;
    
    // In server environment, we can fetch directly from our backend API
    const authHeader = request.headers.get('authorization') || '';
    console.log(`[route.ts] Fetching GRN ${id}. Auth header present: ${!!authHeader}`);

    const response = await fetch(`http://localhost:5000/api/grn/${id}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': authHeader,
      },
      cache: 'no-store',
    });

    console.log(`[route.ts] Backend returned status: ${response.status}`);

    if (!response.ok) {
      throw new Error(`Failed to fetch GRN details from backend. Status: ${response.status}`);
    }

    const data = await response.json();
    
    if (!data.success || !data.data) {
      throw new Error('Invalid data returned from backend');
    }

    // Render the React PDF component to a stream
    const stream = await renderToStream(React.createElement(GrnPdf, { data: data.data }));

    // Create a Node.js ReadableStream from the pdf stream
    const webStream = new ReadableStream({
      start(controller) {
        stream.on('data', (chunk) => controller.enqueue(chunk));
        stream.on('end', () => controller.close());
        stream.on('error', (err) => controller.error(err));
      }
    });

    return new NextResponse(webStream, {
      headers: {
        'Content-Type': 'application/pdf',
        'Content-Disposition': `attachment; filename="GRN_Details_${id}.pdf"`,
      },
    });

  } catch (error) {
    console.error('Error generating GRN PDF:', error);
    return NextResponse.json(
      { error: 'Failed to generate PDF' },
      { status: 500 }
    );
  }
}

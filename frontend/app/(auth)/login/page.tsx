'use client';

import React, { useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as zod from 'zod';
import { useAuth } from '../../../contexts/AuthContext';
import { Lock, Phone, AlertCircle, Loader } from 'lucide-react';

const loginSchema = zod.object({
  mobile: zod.string()
    .min(1, 'Mobile number is required')
    .regex(/^[0-9]+$/, 'Mobile must contain only digits'),
  password: zod.string()
    .min(1, 'Password is required'),
  rememberMe: zod.boolean().optional()
});

type LoginFormValues = zod.infer<typeof loginSchema>;

export default function LoginPage() {
  const { login } = useAuth();
  const [errorMsg, setErrorMsg] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState<boolean>(false);

  const { register, handleSubmit, formState: { errors } } = useForm<LoginFormValues>({
    resolver: zodResolver(loginSchema),
    defaultValues: {
      mobile: '',
      password: '',
      rememberMe: false
    }
  });

  const onSubmit = async (data: LoginFormValues) => {
    setSubmitting(true);
    setErrorMsg(null);
    try {
      await login(data.mobile, data.password);
    } catch (err: any) {
      setErrorMsg(err);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="relative min-h-screen flex items-center justify-center bg-slate-50 px-4 py-12 sm:px-6 lg:px-8 overflow-hidden select-none">
      
      {/* Background Subtle Gradients */}
      <div className="absolute top-1/4 left-1/4 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-cyan-400/10 rounded-full blur-[120px] pointer-events-none"></div>
      <div className="absolute bottom-1/4 right-1/4 translate-x-1/2 translate-y-1/2 w-96 h-96 bg-purple-400/10 rounded-full blur-[120px] pointer-events-none"></div>

      <div className="w-full max-w-md space-y-8 z-10">
        
        {/* Logo and Titles */}
        <div className="text-center">
          <div className="mx-auto h-12 w-12 flex items-center justify-center rounded-xl bg-gradient-to-tr from-cyan-500 to-purple-600 shadow-md shadow-cyan-500/20">
            <span className="text-white font-extrabold text-2xl tracking-tighter">O</span>
          </div>
          <h2 className="mt-6 text-3xl font-extrabold tracking-tight bg-gradient-to-r from-slate-900 via-slate-800 to-slate-700 bg-clip-text text-transparent">
            Welcome back
          </h2>
          <p className="mt-2 text-sm text-slate-500">
            Access your multi-tenant ERP console
          </p>
        </div>

        {/* Login Card */}
        <div className="mt-8 bg-white/80 backdrop-blur-md border border-slate-200/80 rounded-2xl p-8 shadow-xl shadow-slate-200/50">
          
          {errorMsg && (
            <div className="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-3">
              <AlertCircle className="w-5 h-5 text-rose-500 shrink-0 mt-0.5" />
              <div className="text-sm font-semibold text-rose-800">{errorMsg}</div>
            </div>
          )}

          <form className="space-y-6" onSubmit={handleSubmit(onSubmit)}>
            
            {/* Mobile field */}
            <div className="space-y-2">
              <label htmlFor="mobile" className="block text-xs font-semibold uppercase tracking-wider text-slate-500">
                Mobile Number
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                  <Phone className="w-4 h-4 text-slate-400" />
                </div>
                <input
                  id="mobile"
                  type="text"
                  autoComplete="tel"
                  {...register('mobile')}
                  className={`w-full bg-white border ${errors.mobile ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400' : 'border-slate-200 focus:border-cyan-500 focus:ring-cyan-500'} focus:ring-1 rounded-xl py-3 pl-11 pr-4 text-slate-900 placeholder-slate-400 text-sm outline-none transition duration-200`}
                  placeholder="Enter registered mobile"
                />
              </div>
              {errors.mobile && (
                <p className="text-xs text-rose-500 flex items-center gap-1 font-medium">
                  <AlertCircle className="w-3.5 h-3.5" /> {errors.mobile.message}
                </p>
              )}
            </div>

            {/* Password field */}
            <div className="space-y-2">
              <div className="flex justify-between items-center">
                <label htmlFor="password" className="block text-xs font-semibold uppercase tracking-wider text-slate-500">
                  Password
                </label>
                <a href="/forgot-password" className="text-xs font-semibold text-cyan-600 hover:text-cyan-500 transition duration-150">
                  Forgot?
                </a>
              </div>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                  <Lock className="w-4 h-4 text-slate-400" />
                </div>
                <input
                  id="password"
                  type="password"
                  autoComplete="current-password"
                  {...register('password')}
                  className={`w-full bg-white border ${errors.password ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400' : 'border-slate-200 focus:border-cyan-500 focus:ring-cyan-500'} focus:ring-1 rounded-xl py-3 pl-11 pr-4 text-slate-900 placeholder-slate-400 text-sm outline-none transition duration-200`}
                  placeholder="••••••••"
                />
              </div>
              {errors.password && (
                <p className="text-xs text-rose-500 flex items-center gap-1 font-medium">
                  <AlertCircle className="w-3.5 h-3.5" /> {errors.password.message}
                </p>
              )}
            </div>

            {/* Remember Me */}
            <div className="flex items-center justify-between">
              <div className="flex items-center">
                <input
                  id="rememberMe"
                  type="checkbox"
                  {...register('rememberMe')}
                  className="h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500 focus:ring-1 transition duration-150"
                />
                <label htmlFor="rememberMe" className="ml-2 block text-sm text-slate-600 font-medium">
                  Remember me
                </label>
              </div>
            </div>

            {/* Submit button */}
            <button
              type="submit"
              disabled={submitting}
              className="w-full flex items-center justify-center py-3.5 px-4 border border-transparent rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-cyan-600 to-purple-600 hover:from-cyan-500 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500/50 shadow-md shadow-cyan-500/10 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed transition duration-200"
            >
              {submitting ? (
                <Loader className="w-5 h-5 animate-spin" />
              ) : (
                'Sign In'
              )}
            </button>
          </form>
        </div>

        {/* Footer info */}
        <p className="text-center text-xs text-slate-400">
          Powered by Operify. All Rights Reserved. &copy; {new Date().getFullYear()}
        </p>

      </div>
    </div>
  );
}
